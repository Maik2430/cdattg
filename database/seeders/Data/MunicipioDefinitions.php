<?php

namespace Database\Seeders\Data;

final class MunicipioDefinitions
{
    /** @return list<array{municipio: string, departamento_id: int, status: int}> */
    public static function all(): array
    {
        $path = database_path('seeders/Data/municipios.json');
        $contents = file_get_contents($path);

        if ($contents === false) {
            return [];
        }

        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : [];
    }
}
