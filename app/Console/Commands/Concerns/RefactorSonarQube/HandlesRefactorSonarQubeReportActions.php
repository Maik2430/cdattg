<?php

namespace App\Console\Commands\Concerns\RefactorSonarQube;

trait HandlesRefactorSonarQubeReportActions
{
    /**
     * Obtener ruta relativa desde base_path
     */
    private function getRelativePath(string $filePath): string
    {
        return str_replace(base_path().DIRECTORY_SEPARATOR, '', $filePath);
    }

    /**
     * Imprimir reporte final
     */
    private function printReport(bool $dryRun): void
    {
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('📊 REPORTE FINAL');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->newLine();

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Archivos analizados', $this->stats['archivos_analizados']],
                ['Errores encontrados', $this->stats['errores_encontrados']],
                ['Errores corregidos', $dryRun ? 'N/A (dry-run)' : $this->stats['errores_corregidos']],
                ['Archivos modificados', $dryRun ? 'N/A (dry-run)' : count($this->stats['archivos_modificados'])],
            ]
        );

        if (! $dryRun && ! empty($this->stats['archivos_modificados'])) {
            $this->newLine();
            $this->info('📝 Archivos modificados:');
            foreach ($this->stats['archivos_modificados'] as $file) {
                $this->line("  • {$file}");
            }
        }

        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 Modo DRY-RUN: Sin cambios aplicados');
            $this->info('💡 Ejecuta sin --dry-run para aplicar las correcciones');
        } else {
            $this->info('✨ Proceso completado con éxito');
        }
    }
}
