<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Http\Requests\UpdateInstructorRequest;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorUpdateEspecialidadJornadaHelpers
{
    private function prepareUpdateEspecialidades(UpdateInstructorRequest $request, array &$datos): void
    {
        if ($request->has('especialidades') && is_array($request->input('especialidades'))) {
            $especialidadesIds = array_filter($request->input('especialidades', []));

            $especialidadesValidas = \App\Models\RedConocimiento::whereIn('id', $especialidadesIds)
                ->pluck('id')
                ->toArray();

            $especialidadesFormateadas = [
                'principal' => null,
                'secundarias' => [],
            ];

            if (! empty($especialidadesValidas)) {
                $especialidadesFormateadas['principal'] = $especialidadesValidas[0];

                for ($i = 1; $i < count($especialidadesValidas); $i++) {
                    $especialidadesFormateadas['secundarias'][] = $especialidadesValidas[$i];
                }
            }
            $datos['especialidades'] = $especialidadesFormateadas;
        } else {
            $datos['especialidades'] = [
                'principal' => null,
                'secundarias' => [],
            ];
        }
    }

    private function prepareUpdateJornadas(UpdateInstructorRequest $request, array &$datos): array
    {
        $jornadasIds = [];
        if ($request->has('jornadas') && is_array($request->input('jornadas'))) {
            $jornadasRequest = $request->input('jornadas');
            Log::info('Jornadas recibidas en request', [
                'jornadas_request' => $jornadasRequest,
                'tipo' => gettype($jornadasRequest),
                'es_array' => is_array($jornadasRequest),
            ]);

            $jornadasRequest = array_map('intval', array_filter($jornadasRequest));

            if (! empty($jornadasRequest)) {
                $parametrosTemas = \App\Models\ParametroTema::whereIn('id', $jornadasRequest)
                    ->with('parametro')
                    ->get();

                Log::info('Parametros temas encontrados', [
                    'cantidad' => $parametrosTemas->count(),
                    'parametros' => $parametrosTemas->map(function ($pt) {
                        return [
                            'id' => $pt->id,
                            'parametro_name' => $pt->parametro->name ?? null,
                        ];
                    })->toArray(),
                ]);

                foreach ($parametrosTemas as $parametroTema) {
                    if ($parametroTema) {
                        $jornadasIds[] = $parametroTema->id;
                        Log::info('Jornada asignada', [
                            'parametro_tema_id' => $parametroTema->id,
                            'nombre_jornada' => $parametroTema->parametro->name ?? null,
                        ]);
                    }
                }

                $datos['jornadas'] = $jornadasIds;
            } else {
                $datos['jornadas'] = null;
            }
        } else {
            $datos['jornadas'] = null;
        }

        Log::info('Jornadas IDs finales para sincronizar', [
            'jornadas_ids' => $jornadasIds,
            'cantidad' => count($jornadasIds),
        ]);

        return $jornadasIds;
    }
}
