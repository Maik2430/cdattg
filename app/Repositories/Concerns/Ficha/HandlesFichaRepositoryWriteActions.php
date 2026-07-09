<?php

namespace App\Repositories\Concerns\Ficha;

use App\Models\FichaCaracterizacion;

trait HandlesFichaRepositoryWriteActions
{
    public function crear(array $datos): FichaCaracterizacion
    {
        $ficha = FichaCaracterizacion::create($datos);
        $this->invalidarCache();

        return $ficha;
    }

    public function actualizar(int $id, array $datos): bool
    {
        $actualizado = FichaCaracterizacion::where('id', $id)->update($datos);
        $this->invalidarCache();

        return $actualizado;
    }

    public function eliminar(int $id): bool
    {
        $eliminado = FichaCaracterizacion::where('id', $id)->delete();
        $this->invalidarCache();

        return $eliminado;
    }

    public function invalidarCache(): void
    {
        $this->flushCache();
    }
}
