<?php

namespace App\Services\Concerns\Aprendiz;

use App\Events\AprendizAsignadoAFicha;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesAprendizActualizarActions
{
    /**
     * Actualiza un aprendiz existente
     *
     * @throws \Exception
     */
    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $aprendiz = $this->repository->encontrarConRelaciones($id);

            if (! $aprendiz) {
                throw new \Exception('Aprendiz no encontrado.');
            }

            $fichaAnterior = $aprendiz->ficha_caracterizacion_id;
            $fichaNueva = $datos['ficha_caracterizacion_id'] ?? null;

            $actualizado = $this->repository->actualizar($id, $datos);

            $this->repository->invalidarCache();

            if (! empty($fichaNueva) && $fichaNueva != $fichaAnterior) {
                event(new AprendizAsignadoAFicha($aprendiz->fresh(), $fichaNueva));
            }

            $this->manejarRolAprendizEnActualizacion($aprendiz, $fichaAnterior, $fichaNueva);

            Log::info('Aprendiz actualizado exitosamente', [
                'aprendiz_id' => $id,
                'ficha_anterior' => $fichaAnterior,
                'ficha_nueva' => $fichaNueva,
            ]);

            return $actualizado;
        });
    }
}
