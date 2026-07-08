<?php

namespace Database\Seeders;

use Database\Seeders\Data\RolePermissionDefinitions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /** @var array<string, string> */
    private const ROLE_KEYS = [
        'BOT' => 'bot',
        'SUPER ADMINISTRADOR' => 'super_admin',
        'ADMINISTRADOR' => 'admin',
        'VIGILANTE' => 'vigilante',
        'COORDINADOR' => 'coordinador',
        'INSTRUCTOR' => 'instructor',
        'VISITANTE' => 'visitante',
        'APRENDIZ' => 'aprendiz',
        'ASPIRANTE' => 'aspirante',
        'PROVEEDOR' => 'proveedor',
    ];

    public function run(): void
    {
        $config = RolePermissionDefinitions::config();
        $roles = $this->crearRoles($config['roles']);
        $this->crearPermisos($config['permissions']);
        $this->asignarPermisosARoles($roles, $config['role_assignments']);
    }

    /** @param list<string> $roleNames
     * @return array<string, Role>
     */
    private function crearRoles(array $roleNames): array
    {
        $roles = [];

        foreach ($roleNames as $name) {
            $key = self::ROLE_KEYS[$name] ?? strtolower(str_replace(' ', '_', $name));
            $roles[$key] = Role::firstOrCreate(['name' => $name]);
        }

        return $roles;
    }

    /** @param list<string> $permisos */
    private function crearPermisos(array $permisos): void
    {
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }
    }

    /** @param array<string, Role> $roles
     * @param array<string, list<string>> $asignaciones
     */
    private function asignarPermisosARoles(array $roles, array $asignaciones): void
    {
        $roles['super_admin']->syncPermissions(Permission::all());
        $roles['admin']->syncPermissions($asignaciones['admin'] ?? []);
        $roles['coordinador']->syncPermissions($asignaciones['coordinador'] ?? []);
        $roles['instructor']->syncPermissions($asignaciones['instructor'] ?? []);
        $roles['aspirante']->syncPermissions($asignaciones['aspirante'] ?? []);
        $roles['visitante']->syncPermissions($asignaciones['visitante'] ?? []);
        $roles['aprendiz']->syncPermissions($asignaciones['aprendiz'] ?? []);
        $roles['proveedor']->syncPermissions($asignaciones['visitante'] ?? []);
    }
}
