<?php

namespace App\Livewire\Concerns\CreateInstructor;

use Illuminate\Support\Facades\Log;

trait HandlesCreateInstructorStoreExperticiaHelpers
{
    /**
     * @param  array<string, mixed>  $datos
     */
    protected function prepareAreasExperticiaForStore(array &$datos): void
    {
        if (isset($datos['areas_experticia']) && is_array($datos['areas_experticia'])) {
            $valores = array_filter(
                array_map('trim', $datos['areas_experticia']),
                function ($item) {
                    return $item !== null && $item !== '';
                }
            );
            $datos['areas_experticia'] = ! empty($valores) ? array_values($valores) : null;
        } elseif (isset($datos['areas_experticia']) && is_string($datos['areas_experticia'])) {
            $datos['areas_experticia'] = array_filter(array_map('trim', explode("\n", $datos['areas_experticia'])));
            $datos['areas_experticia'] = ! empty($datos['areas_experticia']) ? array_values($datos['areas_experticia']) : null;
        } else {
            $datos['areas_experticia'] = null;
        }
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    protected function prepareCompetenciasTicForStore(array &$datos): void
    {
        if (isset($datos['competencias_tic']) && is_array($datos['competencias_tic'])) {
            $valores = array_filter(
                array_map('trim', $datos['competencias_tic']),
                function ($item) {
                    return $item !== null && $item !== '';
                }
            );
            $datos['competencias_tic'] = ! empty($valores) ? array_values($valores) : null;
        } elseif (isset($datos['competencias_tic']) && is_string($datos['competencias_tic'])) {
            $datos['competencias_tic'] = array_filter(array_map('trim', explode("\n", $datos['competencias_tic'])));
            $datos['competencias_tic'] = ! empty($datos['competencias_tic']) ? array_values($datos['competencias_tic']) : null;
        } else {
            $datos['competencias_tic'] = null;
        }
    }

    /**
     * @return array<int, int>
     */
    protected function prepareModalidadesIdsForStore(): array
    {
        $modalidadesIds = [];

        Log::info('Modalidades recibidas en CreateInstructor', [
            'modalidades_raw' => $this->modalidades,
            'es_array' => is_array($this->modalidades),
            'count' => is_array($this->modalidades) ? count($this->modalidades) : 0,
            'tipo' => gettype($this->modalidades),
        ]);

        if (! is_array($this->modalidades)) {
            $this->modalidades = [];
        }

        if (! empty($this->modalidades)) {
            $modalidadesIds = array_filter($this->modalidades, function ($value) {
                return $value !== null && $value !== '' && $value !== false;
            });
            $modalidadesIds = array_map('intval', $modalidadesIds);
            $modalidadesIds = array_values(array_unique($modalidadesIds));

            Log::info('Modalidades procesadas', [
                'modalidades_ids' => $modalidadesIds,
                'count' => count($modalidadesIds),
            ]);
        } else {
            Log::warning('Modalidades vacías', [
                'modalidades' => $this->modalidades,
                'tipo' => gettype($this->modalidades),
            ]);
        }

        return $modalidadesIds;
    }
}
