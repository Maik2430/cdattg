<?php

namespace Database\Seeders\Data;

final class ParametroDefinitions
{
    /** @return list<array{id: int, name: string, status: int, user_create_id: null, user_edit_id: null}> */
    public static function all(): array
    {
        $path = database_path('seeders/Data/parametros.json');
        $contents = file_get_contents($path);

        if ($contents === false) {
            return [];
        }

        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : [];
    }
}
