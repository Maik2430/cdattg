<?php

namespace App\Console\Commands\Concerns\RefactorSonarQube;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

trait HandlesRefactorSonarQubeFileDiscoveryHelpers
{
    /**
     * Buscar todos los archivos PHP recursivamente
     */
    private function findPhpFiles(string $directory): array
    {
        $files = [];

        if (! is_dir($directory)) {
            return is_file($directory) && pathinfo($directory, PATHINFO_EXTENSION) === 'php'
                ? [$directory]
                : [];
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
