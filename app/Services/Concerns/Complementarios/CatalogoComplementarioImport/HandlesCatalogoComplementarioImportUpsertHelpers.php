<?php

namespace App\Services\Concerns\Complementarios\CatalogoComplementarioImport;

use App\Models\Complementarios\ComplementarioCatalogo;

trait HandlesCatalogoComplementarioImportUpsertHelpers
{
    /**
     * @param  array<string, mixed>  $datos
     */
    private function upsertPrograma(array $datos): int
    {
        $codigo = $datos['prf_codigo'];
        $nuevaVersion = (int) $datos['version'];

        /** @var ComplementarioCatalogo|null $existente */
        $existente = ComplementarioCatalogo::query()
            ->where('prf_codigo', $codigo)
            ->first();

        if ($existente === null) {
            ComplementarioCatalogo::query()->create($datos);

            return 1;
        }

        if ((int) $existente->version >= $nuevaVersion) {
            return 0;
        }

        $existente->fill($datos);
        $existente->save();

        return 1;
    }
}
