<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorSearchActions
{
    /**
     * Búsqueda avanzada de instructores con AJAX
     */
    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            $filtroEstado = $request->input('estado', 'todos');
            $filtroEspecialidad = $request->input('especialidad');
            $filtroRegional = $request->input('regional');
            $page = $request->input('page', 1);

            // Construir query base con relaciones
            $query = Instructor::with([
                'persona',
                'regional',
                'instructorFichas' => function ($q) {
                    $q->with('ficha.programaFormacion');
                },
            ]);

            // Aplicar filtros
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('persona', function ($personaQuery) use ($search) {
                        $personaQuery->where('primer_nombre', 'like', "%{$search}%")
                            ->orWhere('segundo_nombre', 'like', "%{$search}%")
                            ->orWhere('primer_apellido', 'like', "%{$search}%")
                            ->orWhere('segundo_apellido', 'like', "%{$search}%")
                            ->orWhere('numero_documento', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            }

            if ($filtroEstado !== 'todos') {
                if ($filtroEstado === 'activos') {
                    $query->where('status', true);
                } elseif ($filtroEstado === 'inactivos') {
                    $query->where('status', false);
                }
            }

            if ($filtroEspecialidad) {
                $query->whereJsonContains('especialidades', $filtroEspecialidad);
            }

            if ($filtroRegional) {
                $query->where('regional_id', $filtroRegional);
            }

            // Obtener resultados paginados
            $instructores = $query->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $page);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('Instructores.partials.instructores-table', compact('instructores'))->render(),
                    'pagination' => $instructores->links()->toHtml(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Solicitud no válida',
            ]);
        } catch (Exception $e) {
            Log::error('Error en búsqueda de instructores', [
                'error' => $e->getMessage(),
                'filters' => $request->all(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en la búsqueda',
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Error en la búsqueda de instructores.');
        }
    }
}
