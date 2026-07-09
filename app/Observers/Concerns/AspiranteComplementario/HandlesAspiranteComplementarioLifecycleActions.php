<?php

namespace App\Observers\Concerns\AspiranteComplementario;

use App\Models\Complementarios\AspiranteComplementario;

trait HandlesAspiranteComplementarioLifecycleActions
{
    public function updated(AspiranteComplementario $aspirante): void
    {
        // No se requiere acción al actualizar
    }

    public function deleted(AspiranteComplementario $aspirante): void
    {
        // No se requiere acción al eliminar
    }

    public function restored(AspiranteComplementario $aspirante): void
    {
        // No se requiere acción al restaurar
    }

    public function forceDeleted(AspiranteComplementario $aspirante): void
    {
        // No se requiere acción al eliminar permanentemente
    }
}
