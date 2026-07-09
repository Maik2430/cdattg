<?php

namespace App\Console\Commands\Concerns\CleanDuplicateRoles;

use Illuminate\Support\Facades\DB;

trait HandlesCleanDuplicateRolesIdentificationActions
{
    protected function identifyDuplicateRoles(): void
    {
        $this->info('🔍 Identificando usuarios con roles duplicados...');

        $usersWithMultipleRoles = DB::table('users')
            ->join('personas', 'users.persona_id', '=', 'personas.id')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.id', 'personas.primer_nombre', 'personas.segundo_nombre', 'personas.primer_apellido', 'personas.segundo_apellido', 'personas.numero_documento')
            ->groupBy('users.id', 'personas.primer_nombre', 'personas.segundo_nombre', 'personas.primer_apellido', 'personas.segundo_apellido', 'personas.numero_documento')
            ->havingRaw('COUNT(roles.id) > 1')
            ->get();

        $this->info("📊 Encontrados {$usersWithMultipleRoles->count()} usuarios con roles duplicados:");

        foreach ($usersWithMultipleRoles as $user) {
            $roles = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_id', $user->id)
                ->pluck('roles.name')
                ->toArray();

            $nombreCompleto = trim($user->primer_nombre.' '.$user->segundo_nombre.' '.$user->primer_apellido.' '.$user->segundo_apellido);
            $this->line("   - {$nombreCompleto} ({$user->numero_documento}): ".implode(', ', $roles));
        }
        $this->newLine();
    }
}
