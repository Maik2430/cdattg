<?php

namespace App\Repositories\Concerns\Persona;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Collection;

trait HandlesPersonaRepositoryStatisticsActions
{
    public function getEstadisticasPorGenero(): Collection
    {
        return Persona::selectRaw('
                parametros.name as genero,
                COUNT(*) as total
            ')
            ->join('parametros', 'personas.genero', '=', 'parametros.id')
            ->groupBy('personas.genero', 'parametros.name')
            ->orderBy('total', 'desc')
            ->get();
    }

    public function getEstadisticasPorEdad(): Collection
    {
        return Persona::selectRaw('
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) < 18 THEN "Menor de 18"
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 18 AND 25 THEN "18-25 años"
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 26 AND 35 THEN "26-35 años"
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 36 AND 45 THEN "36-45 años"
                    WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 46 AND 55 THEN "46-55 años"
                    ELSE "Mayor de 55"
                END as rango_edad,
                COUNT(*) as total
            ')
            ->groupByRaw('CASE
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) < 18 THEN "Menor de 18"
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 18 AND 25 THEN "18-25 años"
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 26 AND 35 THEN "26-35 años"
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 36 AND 45 THEN "36-45 años"
                WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 46 AND 55 THEN "46-55 años"
                ELSE "Mayor de 55"
            END')
            ->orderBy('total', 'desc')
            ->get();
    }
}
