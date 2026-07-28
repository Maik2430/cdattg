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
        $fallback = [
            'instructor',
            'aprendiz',
            'visitante',
            'administrativo',
            'aspirante',
            'super_administrador',
        ];

        if (DB::getDriverName() === 'sqlite') {
            return $fallback;
        }

        $tipos = self::resolverTiposPersonaDesdeEnumMysql();

        return $tipos !== [] ? $tipos : $fallback;
    }

    /**
     * @return list<string>
     */
    private static function resolverTiposPersonaDesdeEnumMysql(): array
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

        return array_map('trim', str_getcsv($matches[1], ',', "'"));
    }
}
