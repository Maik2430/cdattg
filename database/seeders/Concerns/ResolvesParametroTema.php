<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\DB;

trait ResolvesParametroTema
{
    /**
     * Obtiene el ID de parametros_temas basado en tema_id y parametro_id.
     */
    protected function getParametroTemaId(int $temaId, int $parametroId): ?int
    {
        $parametroTema = DB::table('parametros_temas')
            ->where('tema_id', $temaId)
            ->where('parametro_id', $parametroId)
            ->first();

        return $parametroTema?->id;
    }
}
