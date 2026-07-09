<?php

namespace App\Console\Commands\Concerns\CheckUserPermissions;

use App\Models\User;

trait HandlesCheckUserPermissionsDisplayActions
{
    protected function showUserPermissions($userId): void
    {
        $user = User::with(['persona', 'roles', 'permissions'])->find($userId);

        if (! $user) {
            $this->error("❌ Usuario con ID {$userId} no encontrado.");

            return;
        }

        $this->info("👤 Usuario: {$user->persona->nombre_completo} (ID: {$userId})");
        $this->info("📧 Email: {$user->email}");

        $roles = $user->roles->pluck('name')->toArray();
        if (empty($roles)) {
            $this->warn('⚠️  Sin roles asignados');
        } else {
            $this->info('🎭 Roles asignados: '.implode(', ', $roles));
        }

        $allPermissions = $user->getAllPermissions();

        $this->info('✅ Permisos totales: '.$allPermissions->count());

        if ($allPermissions->isEmpty()) {
            $this->warn('⚠️  No tiene permisos asignados');

            return;
        }

        $grouped = $this->groupPermissions($allPermissions);

        foreach ($grouped as $category => $permissions) {
            $this->newLine();
            $this->info("📦 {$category} ({$permissions->count()}):");
            foreach ($permissions as $permission) {
                $this->line("   ✓ {$permission->name}");
            }
        }
    }

    protected function listAllUsers(): void
    {
        $users = User::with(['persona', 'roles'])->get();

        if ($users->isEmpty()) {
            $this->warn('⚠️  No hay usuarios registrados.');

            return;
        }

        $this->info("👥 LISTADO DE USUARIOS Y ROLES\n");

        $tableData = [];
        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->implode(', ') ?: 'Sin roles';
            $permissionsCount = $user->getAllPermissions()->count();

            $tableData[] = [
                $user->id,
                $user->persona->nombre_completo ?? 'Sin nombre',
                $user->email,
                $roles,
                $permissionsCount,
            ];
        }

        $this->table(
            ['ID', 'Nombre', 'Email', 'Roles', 'Permisos'],
            $tableData
        );

        $this->newLine();
        $this->info('💡 Usa: php artisan user:check-permissions {userId} para ver los permisos detallados de un usuario');
    }
}
