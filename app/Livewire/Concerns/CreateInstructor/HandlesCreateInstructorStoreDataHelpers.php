<?php

namespace App\Livewire\Concerns\CreateInstructor;

use App\Models\ParametroTema;

trait HandlesCreateInstructorStoreDataHelpers
{
    use HandlesCreateInstructorStoreExperticiaHelpers;

    /**
     * @param  array<string, mixed>  $datos
     * @return array<int, int>
     */
    protected function prepareJornadasForStore(array &$datos): array
    {
        $jornadasIds = [];

        if (! empty($this->jornadas)) {
            $parametrosTemas = ParametroTema::whereIn('id', $this->jornadas)
                ->with('parametro')
                ->get();

            foreach ($parametrosTemas as $parametroTema) {
                $jornadasIds[] = $parametroTema->id;
            }

            $datos['jornadas'] = $jornadasIds;
        } else {
            $datos['jornadas'] = null;
        }

        return $jornadasIds;
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    protected function prepareJsonArrayFieldsForStore(array &$datos): void
    {
        $camposJsonArray = [
            'titulos_obtenidos',
            'instituciones_educativas',
            'certificaciones_tecnicas',
            'cursos_complementarios',
            'areas_experticia',
            'competencias_tic',
            'idiomas',
        ];

        foreach ($camposJsonArray as $campo) {
            if ($campo === 'idiomas') {
                $this->prepareIdiomasFieldForStore($datos, $campo);
            } else {
                $this->prepareSimpleJsonArrayFieldForStore($datos, $campo);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    protected function prepareIdiomasFieldForStore(array &$datos, string $campo): void
    {
        if (isset($datos[$campo]) && is_array($datos[$campo])) {
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
            $datos[$campo] = null;
        }
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    protected function prepareSimpleJsonArrayFieldForStore(array &$datos, string $campo): void
    {
        if (isset($datos[$campo]) && is_array($datos[$campo])) {
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
        } else {
            $datos[$campo] = null;
        }
    }
}
