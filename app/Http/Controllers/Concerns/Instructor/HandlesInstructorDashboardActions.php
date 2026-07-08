<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorDashboardActions
{
    /**
     * Dashboard específico para instructores
     */
    public function dashboard(Request $request)
    {
        try {
            $user = Auth::user();
            $instructor = $user->instructor;

            if (! $instructor) {
                return redirect()
                    ->back()
                    ->with('error', 'No se encontró información del instructor');
            }

            // Autorizar acceso
            $this->authorize('viewAny', Instructor::class);

            // Obtener fichas activas
            $fichasActivas = $instructor->instructorFichas()
                ->with(['ficha' => function ($q) {
                    $q->with([
                        'programaFormacion.redConocimiento',
                        'modalidadFormacion',
                        'ambiente.piso.bloque.sede',
                        'jornadaFormacion.parametro',
                        'diasFormacion',
                    ]);
                }])
                ->whereHas('ficha', function ($q) {
                    $q->where('status', true)
                        ->where('fecha_fin', '>=', now()->toDateString());
                })
                ->orderBy('fecha_inicio')
                ->get();

            // Obtener fichas próximas (próximos 30 días)
            $fichasProximas = $instructor->instructorFichas()
                ->with(['ficha' => function ($q) {
                    $q->with([
                        'programaFormacion.redConocimiento',
                        'modalidadFormacion',
                        'ambiente.piso.bloque.sede',
                        'jornadaFormacion.parametro',
                        'diasFormacion',
                    ]);
                }])
                ->whereHas('ficha', function ($q) {
                    $q->where('status', true)
                        ->where('fecha_inicio', '>=', now()->toDateString())
                        ->where('fecha_inicio', '<=', now()->addDays(30)->toDateString());
                })
                ->orderBy('fecha_inicio')
                ->get();

            // Obtener estadísticas de desempeño
            $estadisticas = $this->obtenerEstadisticasDesempeno($instructor);

            // Obtener eventos del calendario (clases)
            $eventosCalendario = $this->obtenerEventosCalendario($instructor);

            // Obtener notificaciones recientes
            $notificaciones = $this->obtenerNotificacionesRecientes();

            // Obtener resumen de actividades
            $actividadesRecientes = $this->obtenerActividadesRecientes($instructor);

            return view(
                'instructores.dashboard',
                compact(
                    'instructor',
                    'fichasActivas',
                    'fichasProximas',
                    'estadisticas',
                    'eventosCalendario',
                    'notificaciones',
                    'actividadesRecientes'
                )
            );
        } catch (Exception $e) {
            Log::error('Error cargando dashboard del instructor', [
                'instructor_id' => Auth::user()->instructor?->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al cargar el dashboard del instructor');
        }
    }
}
