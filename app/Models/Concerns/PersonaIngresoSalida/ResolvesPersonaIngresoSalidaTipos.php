<?php

namespace App\Models\Concerns\PersonaIngresoSalida;

use Illuminate\Support\Facades\DB;

trait ResolvesPersonaIngresoSalidaTipos
{
    /**
     * Obtiene los valores del enum tipo_persona dinámicamente desde la base de datos
     */
    public static function obtenerTiposPersonaDisponibles(): array
    {
        $column = DB::select("SHOW COLUMNS FROM persona_ingreso_salida WHERE Field = 'tipo_persona'");

        if (empty($column)) {
            return [];
        }

        $type = $column[0]->Type;

        preg_match("/^enum\((.*)\)$/", $type, $matches);

        if (empty($matches[1])) {
            return [];
        }

        $values = str_getcsv($matches[1], ',', "'");

        return array_map('trim', $values);
    }
}
