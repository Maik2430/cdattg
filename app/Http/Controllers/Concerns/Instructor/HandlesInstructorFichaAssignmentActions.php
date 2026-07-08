<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait HandlesInstructorFichaAssignmentActions
{
    /**
     * Ver fichas asignadas al instructor
     */
    public function fichasAsignadas(Request $request, ?Instructor $instructor = null)
    {
        $user = Auth::user();

        // Autorizar acceso usando la política
        if ($instructor) {
            $this->authorize('verFichasAsignadas', $instructor);
            $instructorActual = $instructor;
        } else {
            // Si no se especifica instructor, usar el del usuario autenticado
            $instructorActual = $user->instructor;
            if ($instructorActual) {
                $this->authorize('verFichasAsignadas', $instructorActual);
            }
        }

        if (! $instructorActual) {
            return redirect()->back()->with('error', 'No se encontró información del instructor');
        }

        // Obtener parámetros de filtro
        $filtroEstado = $request->get('estado', 'todas');
        $filtroFechaInicio = $request->get('fecha_inicio');
        $filtroFechaFin = $request->get('fecha_fin');
        $filtroPrograma = $request->get('programa');

        // Construir query base con relaciones
        $query = $instructorActual->instructorFichas()
            ->with([
                'ficha' => function ($q) {
                    $q->with([
                        'programaFormacion.redConocimiento',
                        'modalidadFormacion',
                        'ambiente.piso.bloque.sede',
                        'jornadaFormacion.parametro',
                        'diasFormacion',
                    ]);
                },
            ]);

        // Aplicar filtros
        if ($filtroEstado !== 'todas') {
            if ($filtroEstado === 'activas') {
                $query->whereHas('ficha', function ($q) {
                    $q->where('status', true)
                        ->where('fecha_fin', '>=', now()->toDateString());
                });
            } elseif ($filtroEstado === 'finalizadas') {
                $query->whereHas('ficha', function ($q) {
                    $q->where('fecha_fin', '<', now()->toDateString());
                });
            } elseif ($filtroEstado === 'inactivas') {
                $query->whereHas('ficha', function ($q) {
                    $q->where('status', false);
                });
            }
        }

        if ($filtroFechaInicio) {
            $query->whereHas('ficha', function ($q) use ($filtroFechaInicio) {
                $q->where('fecha_inicio', '>=', $filtroFechaInicio);
            });
        }

        if ($filtroFechaFin) {
            $query->whereHas('ficha', function ($q) use ($filtroFechaFin) {
                $q->where('fecha_fin', '<=', $filtroFechaFin);
            });
        }

        if ($filtroPrograma) {
            $query->whereHas('ficha.programaFormacion', function ($q) use ($filtroPrograma) {
                $q->where('nombre', 'like', "%{$filtroPrograma}%");
            });
        }

        // Ordenar por fecha de inicio descendente
        $query->orderBy('fecha_inicio', 'desc');

        // Paginar resultados
        $fichasAsignadas = $query->paginate(15)->withQueryString();

        // Obtener estadísticas
        $resumenFichas = $this->businessRulesService->obtenerResumenFichas($instructorActual);
        $estadisticas = [
            'total' => $resumenFichas['total'],
            'activas' => $resumenFichas['activas'],
            'finalizadas' => $resumenFichas['finalizadas'],
            'total_horas' => $resumenFichas['total_horas'],
        ];

        // Obtener programas únicos para el filtro
        $programas = $instructorActual->instructorFichas()
            ->with('ficha.programaFormacion')
            ->get()
            ->pluck('ficha.programaFormacion.nombre')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view(
            'instructores.fichas-asignadas',
            compact(
                'instructorActual',
                'fichasAsignadas',
                'estadisticas',
                'programas',
                'filtroEstado',
                'filtroFechaInicio',
                'filtroFechaFin',
                'filtroPrograma'
            )
        );
    }
}
