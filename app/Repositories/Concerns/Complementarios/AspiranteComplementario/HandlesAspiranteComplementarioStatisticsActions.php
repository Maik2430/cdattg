<?php

namespace App\Repositories\Concerns\Complementarios\AspiranteComplementario;

use App\Models\Complementarios\AspiranteComplementario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

trait HandlesAspiranteComplementarioStatisticsActions
{
    public function getEstadisticasExclusion(int $programaId): array
    {
        $totalAspirantes = $this->countByPrograma($programaId);
        $rechazados = $this->countByEstado($programaId, 4);

        $sinDocumento = AspiranteComplementario::where('complementario_id', $programaId)
            ->where('estado', '!=', 4)
            ->whereHas('persona', function ($query): void {
                $query->where('condocumento', 0);
            })
            ->count();

        $noRegistradosSofia = AspiranteComplementario::where('complementario_id', $programaId)
            ->where('estado', '!=', 4)
            ->whereHas('persona', function ($query): void {
                $query->where('estado_sofia', 277);
            })
            ->count();

        $validos = AspiranteComplementario::where('complementario_id', $programaId)
            ->where('estado', '!=', 4)
            ->whereHas('persona', function ($query): void {
                $query->where('condocumento', 1)
                    ->where('estado_sofia', '!=', 277);
            })
            ->count();

        return [
            'total' => $totalAspirantes,
            'rechazados' => $rechazados,
            'sin_documento' => $sinDocumento,
            'no_registrados_sofia' => $noRegistradosSofia,
            'validos' => $validos,
        ];
    }

    public function getEstadisticas(): array
    {
        return [
            'total' => AspiranteComplementario::count(),
            'activos' => AspiranteComplementario::where('estado', 1)->count(),
            'aceptados' => AspiranteComplementario::where('estado', 3)->count(),
            'rechazados' => AspiranteComplementario::where('estado', 4)->count(),
        ];
    }

    public function getTendenciaInscripciones(int $meses = 6): Collection
    {
        $isSqlite = DB::getDriverName() === 'sqlite';

        if ($isSqlite) {
            return AspiranteComplementario::selectRaw('
                    CAST(strftime("%Y", created_at) AS INTEGER) as year,
                    CAST(strftime("%m", created_at) AS INTEGER) as month,
                    COUNT(*) as total
                ')
                ->where('created_at', '>=', now()->subMonths($meses))
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();
        }

        return AspiranteComplementario::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as total
            ')
            ->where('created_at', '>=', now()->subMonths($meses))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
    }

    public function getDistribucionPorProgramas(): Collection
    {
        return AspiranteComplementario::selectRaw('
                complementarios_catalogo.denominacion as programa,
                COUNT(*) as total
            ')
            ->join('complementarios_ofertados', 'aspirantes_complementarios.complementario_id', '=', 'complementarios_ofertados.id')
            ->leftJoin('complementarios_catalogo', 'complementarios_ofertados.catalogo_id', '=', 'complementarios_catalogo.id')
            ->groupBy('complementarios_catalogo.denominacion')
            ->orderBy('total', 'desc')
            ->get();
    }
}
