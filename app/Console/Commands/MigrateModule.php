<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\MigrateModule\DefinesMigrateModuleBatches;
use App\Console\Commands\Concerns\MigrateModule\HandlesMigrateModuleFreshDatabaseActions;
use App\Console\Commands\Concerns\MigrateModule\HandlesMigrateModuleMigrationActions;
use Illuminate\Console\Command;

class MigrateModule extends Command
{
    use DefinesMigrateModuleBatches;
    use HandlesMigrateModuleFreshDatabaseActions;
    use HandlesMigrateModuleMigrationActions;

    protected $signature = 'migrate:module
                            {module? : El nombre del módulo a migrar (batch_01_sistema_base, batch_02_permisos, etc.)}
                            {--all : Ejecutar todos los módulos en orden}
                            {--fresh : Ejecutar fresh antes de migrar}
                            {--list : Listar todos los módulos disponibles}';

    protected $description = 'Ejecuta migraciones organizadas por módulos funcionales';

    public function handle(): int
    {
        if ($this->option('list')) {
            return $this->listModules();
        }

        if ($this->option('all')) {
            if ($this->option('fresh')) {
                $this->freshDatabase();
            }

            return $this->migrateAll();
        }

        if ($this->option('fresh')) {
            $this->freshDatabase();
        }

        $module = $this->argument('module');

        if (! $module) {
            $this->error('❌ Debes especificar un módulo o usar --all');
            $this->info('💡 Usa: php artisan migrate:module --list para ver todos los módulos');

            return 1;
        }

        return $this->migrateSingleBatch($module);
    }
}
