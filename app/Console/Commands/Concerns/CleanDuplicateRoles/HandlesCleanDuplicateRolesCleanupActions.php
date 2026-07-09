<?php

namespace App\Console\Commands\Concerns\CleanDuplicateRoles;

use App\Models\Aprendiz;
use App\Models\Instructor;
use App\Models\User;

trait HandlesCleanDuplicateRolesCleanupActions
{
    protected function cleanupInstructorRoles($dryRun = false): void
    {
        $this->info('👨‍🏫 Limpiando roles de instructores...');

        $instructores = Instructor::with(['persona.user'])->get();
        $cleaned = 0;

        foreach ($instructores as $instructor) {
            if ($instructor->persona && $instructor->persona->user) {
                $user = $instructor->persona->user;

                if (! $dryRun) {
                    $user->syncRoles(['INSTRUCTOR']);
                }

                $cleaned++;
                $this->line("   ✅ {$instructor->persona->nombre_completo}: ".
                    ($dryRun ? 'Se asignaría solo INSTRUCTOR' : 'Solo rol INSTRUCTOR'));
            }
        }

        $this->info("📈 Instructores procesados: {$cleaned}");
        $this->newLine();
    }

    protected function cleanupAprendizRoles($dryRun = false): void
    {
        $this->info('👨‍🎓 Limpiando roles de aprendices...');

        $aprendices = Aprendiz::with(['persona.user'])->get();
        $cleaned = 0;

        foreach ($aprendices as $aprendiz) {
            if ($aprendiz->persona && $aprendiz->persona->user) {
                $user = $aprendiz->persona->user;

                if (! $dryRun) {
                    $user->syncRoles(['APRENDIZ']);
                }

                $cleaned++;
                $this->line("   ✅ {$aprendiz->persona->nombre_completo}: ".
                    ($dryRun ? 'Se asignaría solo APRENDIZ' : 'Solo rol APRENDIZ'));
            }
        }

        $this->info("📈 Aprendices procesados: {$cleaned}");
        $this->newLine();
    }

    protected function cleanupOrphanedRoles($dryRun = false): void
    {
        $this->info('🧹 Limpiando roles huérfanos...');

        $orphanedUsers = User::whereDoesntHave('persona.instructor')
            ->whereDoesntHave('persona.aprendiz')
            ->whereHas('roles')
            ->with('persona')
            ->get();

        $cleaned = 0;

        foreach ($orphanedUsers as $user) {
            if ($user->persona) {
                if (! $dryRun) {
                    $user->syncRoles(['VISITANTE']);
                }

                $cleaned++;
                $this->line("   ✅ {$user->persona->nombre_completo}: ".
                    ($dryRun ? 'Se asignaría solo VISITANTE' : 'Solo rol VISITANTE'));
            }
        }

        $this->info("📈 Usuarios huérfanos procesados: {$cleaned}");
        $this->newLine();
    }
}
