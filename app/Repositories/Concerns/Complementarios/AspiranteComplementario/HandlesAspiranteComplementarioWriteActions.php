<?php

namespace App\Repositories\Concerns\Complementarios\AspiranteComplementario;

use App\Models\Complementarios\AspiranteComplementario;

trait HandlesAspiranteComplementarioWriteActions
{
    public function create(array $data): AspiranteComplementario
    {
        return AspiranteComplementario::create($data);
    }

    public function update(AspiranteComplementario $aspirante, array $data): bool
    {
        return $aspirante->update($data);
    }

    public function delete(AspiranteComplementario $aspirante): bool
    {
        return $aspirante->update(['estado' => 4]);
    }
}
