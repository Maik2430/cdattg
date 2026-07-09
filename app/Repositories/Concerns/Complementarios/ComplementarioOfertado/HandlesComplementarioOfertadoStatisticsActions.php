<?php

namespace App\Repositories\Concerns\Complementarios\ComplementarioOfertado;

use App\Models\Complementarios\ComplementarioOfertado;
use Illuminate\Database\Eloquent\Collection;

trait HandlesComplementarioOfertadoStatisticsActions
{
    public function getEstadisticas(): array
    {
        $sinOfertaId = $this->getEstadoIdByLegacyValue(0);
        $activosId = $this->getEstadoIdByLegacyValue(1);
        $cuposLlenosId = $this->getEstadoIdByLegacyValue(2);

        return [
            'total' => ComplementarioOfertado::count(),
            'activos' => $activosId ? ComplementarioOfertado::where('estado_id', $activosId)->count() : 0,
            'sin_oferta' => $sinOfertaId ? ComplementarioOfertado::where('estado_id', $sinOfertaId)->count() : 0,
            'cupos_llenos' => $cuposLlenosId ? ComplementarioOfertado::where('estado_id', $cuposLlenosId)->count() : 0,
        ];
    }

    public function getProgramasConMayorDemanda(int $limit = 10): Collection
    {
        return ComplementarioOfertado::selectRaw('
                complementarios_ofertados.id,
                complementarios_ofertados.codigo,
                complementarios_catalogo.denominacion as nombre,
                complementarios_catalogo.duracion_horas as duracion,
                complementarios_ofertados.cupos,
                complementarios_ofertados.estado_id,
                complementarios_catalogo.modalidad_id,
                complementarios_ofertados.jornada_id,
                complementarios_ofertados.ambiente_id,
                complementarios_ofertados.justificacion,
                complementarios_catalogo.requisitos_ingreso,
                complementarios_ofertados.created_at,
                complementarios_ofertados.updated_at,
                COUNT(aspirantes_complementarios.id) as total_aspirantes,
                SUM(CASE WHEN aspirantes_complementarios.estado = 3 THEN 1 ELSE 0 END) as aceptados,
                SUM(CASE WHEN aspirantes_complementarios.estado = 1 THEN 1 ELSE 0 END) as pendientes
            ')
            ->leftJoin('complementarios_catalogo', 'complementarios_ofertados.catalogo_id', '=', 'complementarios_catalogo.id')
            ->leftJoin('aspirantes_complementarios', 'complementarios_ofertados.id', '=', 'aspirantes_complementarios.complementario_id')
            ->groupBy(
                'complementarios_ofertados.id',
                'complementarios_ofertados.codigo',
                'complementarios_catalogo.denominacion',
                'complementarios_catalogo.duracion_horas',
                'complementarios_ofertados.cupos',
                'complementarios_ofertados.estado_id',
                'complementarios_catalogo.modalidad_id',
                'complementarios_ofertados.jornada_id',
                'complementarios_ofertados.ambiente_id',
                'complementarios_ofertados.justificacion',
                'complementarios_catalogo.requisitos_ingreso',
                'complementarios_ofertados.created_at',
                'complementarios_ofertados.updated_at'
            )
            ->orderBy('total_aspirantes', 'desc')
            ->limit($limit)
            ->get();
    }
}
