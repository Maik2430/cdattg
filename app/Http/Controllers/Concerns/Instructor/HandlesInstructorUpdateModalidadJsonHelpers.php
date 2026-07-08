<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Http\Requests\UpdateInstructorRequest;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorUpdateModalidadJsonHelpers
{
    private function prepareUpdateModalidades(UpdateInstructorRequest $request): array
    {
        $modalidadesIds = [];
        Log::info('Modalidades recibidas en request', [
            'has_modalidades' => $request->has('modalidades'),
            'modalidades_raw' => $request->input('modalidades'),
            'es_array' => is_array($request->input('modalidades')),
        ]);

        if ($request->has('modalidades') && is_array($request->input('modalidades'))) {
            $modalidadesRequest = array_map('intval', array_filter($request->input('modalidades')));
            Log::info('Modalidades procesadas del request', [
                'modalidades_request' => $modalidadesRequest,
                'count' => count($modalidadesRequest),
            ]);

            if (! empty($modalidadesRequest)) {
                $parametrosTemas = \App\Models\ParametroTema::whereIn('id', $modalidadesRequest)->get();
                $modalidadesIds = $parametrosTemas->pluck('id')->toArray();
                Log::info('Parametros temas encontrados para modalidades', [
                    'modalidades_ids' => $modalidadesIds,
                    'count' => count($modalidadesIds),
                ]);
            }
        } else {
            Log::warning('No se recibieron modalidades en el request o no es array');
        }

        return $modalidadesIds;
    }

    private function prepareUpdateJsonFields(array &$datos): void
    {
        $camposJsonArray = [
            'titulos_obtenidos',
            'instituciones_educativas',
            'certificaciones_tecnicas',
            'cursos_complementarios',
            'idiomas',
        ];

        foreach ($camposJsonArray as $campo) {
            Log::info("Procesando campo array: {$campo}", [
                'existe' => isset($datos[$campo]),
                'es_array' => isset($datos[$campo]) && is_array($datos[$campo]),
                'valor' => $datos[$campo] ?? 'no existe',
            ]);

            if (isset($datos[$campo]) && is_array($datos[$campo])) {
                if ($campo === 'idiomas') {
                    $idiomasFiltrados = [];
                    foreach ($datos[$campo] as $idioma) {
                        if (is_array($idioma) && isset($idioma['idioma']) && ! empty(trim($idioma['idioma'] ?? ''))) {
                            $idiomasFiltrados[] = [
                                'idioma' => trim($idioma['idioma']),
                                'nivel' => $idioma['nivel'] ?? null,
                            ];
                        }
                    }
                    $datos[$campo] = ! empty($idiomasFiltrados) ? $idiomasFiltrados : null;
                } else {
                    $valores = array_filter(
                        array_map(function ($item) {
                            if (is_string($item)) {
                                return trim($item);
                            } elseif (is_scalar($item)) {
                                return (string) $item;
                            }

                            return null;
                        }, $datos[$campo]),
                        function ($item) {
                            return $item !== null && $item !== '';
                        }
                    );
                    $datos[$campo] = ! empty($valores) ? array_values($valores) : null;
                }

                Log::info("Campo procesado: {$campo}", [
                    'resultado' => $datos[$campo],
                ]);
            } else {
                $datos[$campo] = null;
            }
        }

        if (isset($datos['areas_experticia']) && is_string($datos['areas_experticia'])) {
            $datos['areas_experticia'] = array_filter(array_map('trim', explode("\n", $datos['areas_experticia'])));
            $datos['areas_experticia'] = ! empty($datos['areas_experticia']) ? array_values($datos['areas_experticia']) : null;
        }

        if (isset($datos['competencias_tic']) && is_string($datos['competencias_tic'])) {
            $datos['competencias_tic'] = array_filter(array_map('trim', explode("\n", $datos['competencias_tic'])));
            $datos['competencias_tic'] = ! empty($datos['competencias_tic']) ? array_values($datos['competencias_tic']) : null;
        }
    }
}
