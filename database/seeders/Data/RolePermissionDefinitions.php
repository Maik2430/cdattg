<?php

namespace Database\Seeders\Data;

final class RolePermissionDefinitions
{
    /** @return array{permissions: list<string>, role_assignments: array<string, list<string>>, roles: list<string>} */
    public static function config(): array
    {
        $path = database_path('seeders/Data/role_permissions.json');
        $contents = file_get_contents($path);

        if ($contents === false) {
            return ['permissions' => [], 'role_assignments' => [], 'roles' => []];
        }

        $decoded = json_decode($contents, true);

        if (! is_array($decoded)) {
            return ['permissions' => [], 'role_assignments' => [], 'roles' => []];
        }

        return [
            'permissions' => $decoded['permissions'] ?? [],
            'role_assignments' => $decoded['role_assignments'] ?? [],
            'roles' => $decoded['roles'] ?? [],
        ];
    }
}
