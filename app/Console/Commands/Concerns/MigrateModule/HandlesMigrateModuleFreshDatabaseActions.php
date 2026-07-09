<?php

namespace App\Console\Commands\Concerns\MigrateModule;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait HandlesMigrateModuleFreshDatabaseActions
{
    protected function freshDatabase(): void
    {
        $this->warn('⚠️  Limpiando base de datos...');

        try {
            Schema::disableForeignKeyConstraints();

            $tableNames = $this->resolveTableNamesForFreshDatabase();

            if ($tableNames === null) {
                Schema::enableForeignKeyConstraints();

                return;
            }

            if (empty($tableNames)) {
                $this->info('  ℹ No hay tablas para eliminar');
                Schema::enableForeignKeyConstraints();

                return;
            }

            foreach ($tableNames as $table) {
                try {
                    Schema::dropIfExists($table);
                    $this->line("  ✓ Eliminada tabla: {$table}");
                } catch (\Exception $e) {
                    $this->warn("  ⚠ No se pudo eliminar la tabla {$table}: {$e->getMessage()}");
                }
            }

            Schema::enableForeignKeyConstraints();

            $this->info('✓ Base de datos limpiada exitosamente');
        } catch (\Exception $e) {
            Schema::enableForeignKeyConstraints();
            $this->error("❌ Error al limpiar la base de datos: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * @return array<int, string>|null
     */
    private function resolveTableNamesForFreshDatabase(): ?array
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            $tables = DB::select('SHOW TABLES');
            $tableNames = [];
            foreach ($tables as $table) {
                $tableArray = (array) $table;
                $tableNames[] = reset($tableArray);
            }

            return $tableNames;
        }

        if ($driver === 'pgsql') {
            $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");

            return array_map(static fn ($table) => $table->tablename, $tables);
        }

        if ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name != 'sqlite_sequence'");

            return array_map(static fn ($table) => $table->name, $tables);
        }

        $this->warn("⚠️  Driver de base de datos no soportado: {$driver}");

        return null;
    }
}
