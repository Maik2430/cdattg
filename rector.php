<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

/**
 * Configuración inicial de Rector para CDATTG.
 *
 * Fase de adopción: se usa SOLO en modo --dry-run (reporte).
 * No se aplican cambios automáticos todavía; el objetivo es medir qué
 * refactors seguros (tipos, imports, código muerto) propone la herramienta
 * antes de habilitar su aplicación por módulo.
 */
return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
    ])
    ->withSkip([
        // Migraciones modulares: no se refactorizan automáticamente.
        __DIR__ . '/app/*/migrations/*',
    ])
    ->withPhpVersion(\Rector\ValueObject\PhpVersion::PHP_83)
    // Conjuntos seguros: declaración de tipos y limpieza de código muerto.
    ->withPreparedSets(
        deadCode: true,
        typeDeclarations: true,
    )
    // Importa nombres de clase totalmente cualificados y elimina imports sin uso.
    ->withImportNames(
        removeUnusedImports: true,
    );
