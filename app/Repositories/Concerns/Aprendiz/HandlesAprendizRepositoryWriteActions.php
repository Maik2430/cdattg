<?php

namespace App\Repositories\Concerns\Aprendiz;

use App\Models\Aprendiz;

trait HandlesAprendizRepositoryWriteActions
{
    public function crear(array $datos): Aprendiz
    {
        return Aprendiz::create($datos);
    }

    public function actualizar(int $id, array $datos): bool
    {
        return Aprendiz::where('id', $id)->update($datos);
    }

    public function eliminar(int $id): bool
    {
        $aprendiz = Aprendiz::find($id);

        return $aprendiz ? $aprendiz->delete() : false;
    }

    public function invalidarCache(): void
    {
        $this->flushCache();
    }
}
