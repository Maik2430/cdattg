<?php

namespace App\Console\Commands\Concerns\RefactorSonarQube;

trait HandlesRefactorSonarQubeAnalysisActions
{
    /**
     * Analizar un archivo y aplicar correcciones
     */
    private function analyzeFile(string $filePath, bool $dryRun): void
    {
        $this->stats['archivos_analizados']++;

        $content = file_get_contents($filePath);
        $originalContent = $content;
        $erroresEnArchivo = 0;

        $content = $this->fixTrailingWhitespace($content, $erroresEnArchivo);
        $content = $this->fixCountToEmpty($content, $erroresEnArchivo);

        if ($content !== $originalContent) {
            $this->stats['errores_encontrados'] += $erroresEnArchivo;

            if (! $dryRun) {
                file_put_contents($filePath, $content);
                $this->stats['errores_corregidos'] += $erroresEnArchivo;
                $this->stats['archivos_modificados'][] = $this->getRelativePath($filePath);
            }
        }
    }

    /**
     * Corregir espacios en blanco al final de líneas
     */
    private function fixTrailingWhitespace(string $content, int &$count): string
    {
        $lines = explode("\n", $content);
        $fixed = false;

        foreach ($lines as &$line) {
            if (preg_match('/\s+$/', $line)) {
                $line = rtrim($line);
                $count++;
                $fixed = true;
            }
        }

        return $fixed ? implode("\n", $lines) : $content;
    }

    /**
     * Reemplazar !empty($array) con !empty($array)
     */
    private function fixCountToEmpty(string $content, int &$count): string
    {
        $patterns = [
            '/count\((\$\w+)\)\s*>\s*0/' => '!empty($1)',
            '/count\((\$\w+)\)\s*==\s*0/' => 'empty($1)',
            '/count\((\$\w+)\)\s*===\s*0/' => 'empty($1)',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $newContent = preg_replace($pattern, $replacement, $content, -1, $replacements);
            if ($replacements > 0) {
                $content = $newContent;
                $count += $replacements;
            }
        }

        return $content;
    }
}
