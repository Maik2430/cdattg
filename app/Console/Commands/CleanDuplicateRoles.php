<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\CleanDuplicateRoles\HandlesCleanDuplicateRolesCleanupActions;
use App\Console\Commands\Concerns\CleanDuplicateRoles\HandlesCleanDuplicateRolesIdentificationActions;
use App\Console\Commands\Concerns\CleanDuplicateRoles\HandlesCleanDuplicateRolesReportActions;
use Illuminate\Console\Command;

class CleanDuplicateRoles extends Command
{
    use HandlesCleanDuplicateRolesCleanupActions;
    use HandlesCleanDuplicateRolesIdentificationActions;
    use HandlesCleanDuplicateRolesReportActions;

    protected $signature = 'roles:cleanup {--dry-run : Solo mostrar qué se haría sin ejecutar cambios}';

    protected $description = 'Limpia roles duplicados y asigna roles correctos según las relaciones del sistema';

    public function handle(): void
    {
        $this->info('🧹 Iniciando limpieza de roles duplicados...');
        $this->newLine();

        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 MODO DRY-RUN: Solo se mostrarán los cambios que se harían');
            $this->newLine();
        }

        $this->identifyDuplicateRoles();
        $this->cleanupInstructorRoles($dryRun);
        $this->cleanupAprendizRoles($dryRun);
        $this->cleanupOrphanedRoles($dryRun);
        $this->generateReport();

        $this->newLine();
        $this->info('✅ Limpieza completada exitosamente!');
    }
}
