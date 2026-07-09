<?php

namespace App\Console\Commands\Concerns\RefactorSonarQube;

use Illuminate\Console\Command;

trait HandlesRefactorSonarQubeExecutionActions
{
    /**
     * Ejecutar comando de consola
     */
    public function handle(): int
    {
        if (! app()->environment(['local', 'development', 'testing'])) {
            $this->error('❌ Este comando solo puede ejecutarse en entorno de desarrollo');

            return Command::FAILURE;
        }

        $dryRun = $this->option('dry-run');
        $targetPath = $this->option('path');

        $this->info('🤖 Agente de Refactorización SonarQube');
        $this->line('📁 Ruta base: '.base_path());
        $this->line('🎯 Analizando: '.$targetPath);

        if ($dryRun) {
            $this->warn('🔍 Modo DRY-RUN (sin cambios)');
        } else {
            $this->info('✏️  Modo CORRECCIÓN (aplicará cambios)');
        }

        $this->newLine();

        $fullPath = base_path($targetPath);

        if (! file_exists($fullPath)) {
            $this->error("❌ La ruta {$targetPath} no existe");

            return Command::FAILURE;
        }

        $files = $this->findPhpFiles($fullPath);

        if (! empty($files)) {
            $progressBar = $this->output->createProgressBar(count($files));
            $progressBar->setFormat('verbose');

            foreach ($files as $file) {
                $this->analyzeFile($file, $dryRun);
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);
        } else {
            $this->warn('⚠️  No se encontraron archivos PHP en la ruta especificada');
        }

        $this->printReport($dryRun);

        return Command::SUCCESS;
    }
}
