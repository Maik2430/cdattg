<?php

namespace Database\Seeders\Data;

final class AmbienteDefinitions
{
    /** @return list<array{id: int, title: string, piso_id: int}> */
    public static function all(): array
    {
        $path = database_path('seeders/Data/ambientes.json');
        $contents = file_get_contents($path);

        if ($contents === false) {
            return [];
        }

        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : [];
    }
}
