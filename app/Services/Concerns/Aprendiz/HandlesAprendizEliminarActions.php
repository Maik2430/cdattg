<?php

namespace App\Services\Concerns\Aprendiz;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesAprendizEliminarActions
{
    /**
     * Elimina un aprendiz (soft delete)
     *
     * @throws \Exception
     */
    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $aprendiz = $this->repository->encontrarConRelaciones($id);

            $eliminado = $this->repository->eliminar($id);

            if ($eliminado) {
                $this->repository->invalidarCache();

                if ($aprendiz) {
                    $this->removerRolAprendizAlEliminar($aprendiz, $id);
                }

                Log::info('Aprendiz eliminado exitosamente', [
                    'aprendiz_id' => $id,
                ]);
            }

            return $eliminado;
        });
    }
}
