<?php

namespace App\Services\Concerns\Aprendiz;

use Illuminate\Support\Facades\Log;

trait HandlesAprendizEstadoActions
{
    /**
     * Cambia el estado de un aprendiz
     *
     * @throws \Exception
     */
    public function cambiarEstado(int $id): bool
    {
        $aprendiz = $this->repository->encontrarConRelaciones($id);

        if (! $aprendiz) {
            throw new \Exception('Aprendiz no encontrado.');
        }

        $nuevoEstado = ! $aprendiz->estado;

        $actualizado = $this->repository->actualizar($id, ['estado' => $nuevoEstado]);

        Log::info('Estado de aprendiz cambiado', [
            'aprendiz_id' => $id,
            'nuevo_estado' => $nuevoEstado,
        ]);

        return $actualizado;
    }
}
