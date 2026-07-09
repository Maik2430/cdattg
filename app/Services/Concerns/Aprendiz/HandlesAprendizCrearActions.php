<?php

namespace App\Services\Concerns\Aprendiz;

use App\Events\AprendizAsignadoAFicha;
use App\Models\Aprendiz;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesAprendizCrearActions
{
    /**
     * Crea un nuevo aprendiz con validaciones de negocio
     *
     * @throws \Exception
     */
    public function crear(array $datos): Aprendiz
    {
        return DB::transaction(function () use ($datos) {
            if ($this->repository->esAprendiz($datos['persona_id'])) {
                throw new \Exception('Esta persona ya está registrada como aprendiz.');
            }

            $aprendiz = $this->repository->crear($datos);

            $this->repository->invalidarCache();

            if (! empty($datos['ficha_caracterizacion_id'])) {
                event(new AprendizAsignadoAFicha($aprendiz, $datos['ficha_caracterizacion_id']));
            }

            $this->asignarRolAprendizAlCrear($aprendiz, $datos);

            Log::info('Aprendiz creado exitosamente', [
                'aprendiz_id' => $aprendiz->id,
                'persona_id' => $datos['persona_id'],
                'ficha_id' => $datos['ficha_caracterizacion_id'] ?? null,
            ]);

            return $aprendiz;
        });
    }
}
