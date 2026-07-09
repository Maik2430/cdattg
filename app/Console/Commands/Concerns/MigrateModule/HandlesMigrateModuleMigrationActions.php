<?php

namespace App\Console\Commands\Concerns\MigrateModule;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\Artisan;

trait HandlesMigrateModuleMigrationActions
{
    protected function listModules(): int
    {
        $this->info('📋 Módulos de migración disponibles:');
        $this->newLine();

        foreach ($this->batches as $key => $description) {
            $path = database_path("migrations/{$key}");
            $exists = is_dir($path);
            $status = $exists ? '✓' : '✗';

            $this->line("  {$status} <fg=cyan>{$key}</> - {$description}");
        }

        $this->newLine();
        $this->info('💡 Uso:');
        $this->line('  php artisan migrate:module batch_01_sistema_base');
        $this->line('  php artisan migrate:module --all');
        $this->line('  php artisan migrate:module --all --fresh');

        return 0;
    }

    protected function migrateAll(): int
    {
        if (! $this->hasPendingMigrations()) {
            $exitCode = Artisan::call('migrate', ['--force' => true]);
            $output = trim(Artisan::output());

            if ($output !== '') {
                $this->line($output);
            }

            if ($exitCode === 0) {
                $this->info('✅ Todas las migraciones completadas exitosamente');
            }

            return $exitCode;
        }

        $this->info('🚀 Ejecutando todas las migraciones por módulos...');
        $this->newLine();

        $totalBatches = count($this->batches);
        $currentBatch = 0;

        foreach ($this->batches as $batch => $description) {
            $currentBatch++;
            $this->info("[{$currentBatch}/{$totalBatches}] Migrando: {$batch}");

            $result = $this->migrateSingleBatch($batch, false);

            if ($result !== 0) {
                $this->error("❌ Error al migrar el batch: {$batch}");

                return 1;
            }

            $this->newLine();
        }

        $this->info('✅ Todas las migraciones completadas exitosamente');

        return 0;
    }

    protected function migrateSingleBatch(string $batch, bool $showHeader = true): int
    {
        if (! array_key_exists($batch, $this->batches)) {
            $this->error("❌ El batch '{$batch}' no existe");
            $this->info('💡 Usa: php artisan migrate:module --list para ver todos los módulos');

            return 1;
        }

        $path = "database/migrations/{$batch}";
        $fullPath = base_path($path);

        if (! is_dir($fullPath)) {
            $this->error("❌ El directorio del módulo no existe: {$path}");

            return 1;
        }

        if ($showHeader) {
            $this->info("🔄 Migrando batch: {$batch}");
            $this->line("   {$this->batches[$batch]}");
            $this->newLine();
        }

        try {
            $exitCode = Artisan::call('migrate', [
                '--path' => $path,
                '--force' => true,
            ]);

            $output = Artisan::output();
            if (! empty(trim($output))) {
                $this->line($output);
            }

            if ($exitCode === 0) {
                $this->info("✓ Batch {$batch} migrado exitosamente");

                return 0;
            }

            $this->error("❌ Error al migrar el batch: {$batch}");

            return 1;
        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");

            return 1;
        }
    }

    protected function hasPendingMigrations(): bool
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('migrations')) {
            return true;
        }

        /** @var Migrator $migrator */
        $migrator = app('migrator');
        $repository = app('migration.repository');

        foreach (array_keys($this->batches) as $batch) {
            $path = database_path("migrations/{$batch}");

            if (! is_dir($path)) {
                continue;
            }

            $files = $migrator->getMigrationFiles([$path]);
            $ran = $repository->getRan();

            foreach ($files as $file) {
                if (! in_array($migrator->getMigrationName($file), $ran, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
