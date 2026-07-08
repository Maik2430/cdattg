<?php

namespace Database\Seeders\Data;

final class TemaDefinitions
{
    /** @return list<array{id: int, name: string, paramIds: list<int>}> */
    public static function all(): array
    {
        $path = database_path('seeders/Data/temas.json');
        $contents = file_get_contents($path);

        if ($contents === false) {
            return [];
        }

        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : [];
    }
}
