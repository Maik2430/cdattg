<?php

namespace App\Http\Controllers\Concerns\Aprendiz;

use App\Models\Aprendiz;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesAprendizDatatableActions
{
    /**
     * DataTable server-side para aprendices
     */
    public function datatable(Request $request): JsonResponse
    {
        try {
            $query = Aprendiz::with(['persona.tipoDocumento', 'fichaCaracterizacion.programaFormacion'])
                ->whereIn('id', function ($subquery) {
                    $subquery->select(DB::raw('MAX(id)'))
                        ->from('aprendices')
                        ->groupBy('persona_id');
                });

            if ($search = $request->input('search.value')) {
                $query->whereHas('persona', function ($q) use ($search) {
                    $q->where('primer_nombre', 'LIKE', "%{$search}%")
                        ->orWhere('segundo_nombre', 'LIKE', "%{$search}%")
                        ->orWhere('primer_apellido', 'LIKE', "%{$search}%")
                        ->orWhere('segundo_apellido', 'LIKE', "%{$search}%")
                        ->orWhere('numero_documento', 'LIKE', "%{$search}%");
                });
            }

            $totalRecords = $query->count();

            if ($request->has('order')) {
                $orderColumn = $request->input('order.0.column');
                $orderDir = $request->input('order.0.dir');

                $columns = ['id', 'persona.numero_documento', 'persona.primer_nombre', 'fichaCaracterizacion.ficha', 'estado'];
                if (isset($columns[$orderColumn])) {
                    if (str_contains($columns[$orderColumn], '.')) {
                        [$relation, $column] = explode('.', $columns[$orderColumn]);
                        $query->join($relation === 'persona' ? 'personas' : 'ficha_caracterizacions',
                            'aprendices.'.$relation.'_id', '=', $relation === 'persona' ? 'personas.id' : 'ficha_caracterizacions.id')
                            ->orderBy($column, $orderDir);
                    } else {
                        $query->orderBy($columns[$orderColumn], $orderDir);
                    }
                }
            }

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $aprendices = $query->skip($start)->take($length)->get();

            $data = $aprendices->map(function ($aprendiz) {
                return [
                    'id' => $aprendiz->id,
                    'documento' => $aprendiz->persona->numero_documento ?? 'N/A',
                    'nombre' => $aprendiz->persona->nombre_completo ?? 'N/A',
                    'ficha' => $aprendiz->fichaCaracterizacion->ficha ?? 'N/A',
                    'programa' => $aprendiz->fichaCaracterizacion->programaFormacion->nombre ?? 'N/A',
                    'estado' => $aprendiz->estado ? 'Activo' : 'Inactivo',
                    'acciones' => $aprendiz->id,
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            Log::error('Error en DataTable de aprendices: '.$e->getMessage());

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error al cargar los datos',
            ], 500);
        }
    }
}
