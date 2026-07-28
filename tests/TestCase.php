<?php

namespace Tests;

use App\Exceptions\MigrationBatchException;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Configurar APP_KEY para tests si no está configurado
        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:' . base64_encode('12345678901234567890123456789012')]);
        }
    }

    /**
     * Ruta del SQLite de testing.
     * Respeta DB_DATABASE / TESTING_DB (p. ej. cobertura Docker en /tmp)
     * para no pisar ni compartir database/testing.sqlite con otros procesos.
     */
    protected function testingDatabasePath(): string
    {
        $fromEnv = getenv('TESTING_DB') ?: getenv('DB_DATABASE');

        if (is_string($fromEnv) && $fromEnv !== '' && $fromEnv !== ':memory:') {
            if ($this->isAbsolutePath($fromEnv)) {
                return $fromEnv;
            }

            return base_path($fromEnv);
        }

        return database_path('testing.sqlite');
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, '/')
            || (strlen($path) > 2 && ctype_alpha($path[0]) && $path[1] === ':' && ($path[2] === '\\' || $path[2] === '/'));
    }

    /**
     * Sobrescribe el método migrateDatabases() que usa RefreshDatabase.
     * Esto asegura que las migraciones se ejecuten en el orden correcto según los batches
     * en lugar del orden alfabético por nombre de archivo.
     *
     * @return void
     */
    protected function migrateDatabases()
    {
        $connection = $this->prepareTestingConnection();
        $driver = config("database.connections.{$connection}.driver");

        $this->dropAllTables($connection, $driver);
        $this->runMigrationBatches($connection);
    }

    private function prepareTestingConnection(): string
    {
        $databasePath = $this->testingDatabasePath();
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $databasePath]);
        DB::purge('sqlite');

        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'sqlite' && $databasePath !== ':memory:' && ! file_exists($databasePath)) {
            $dir = dirname($databasePath);
            if (! is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            touch($databasePath);
        }

        DB::reconnect($connection);
        $this->configureSqliteConnection($connection, $driver);

        return $connection;
    }

    private function dropAllTables(string $connection, string $driver): void
    {
        if ($driver === 'sqlite') {
            $this->dropSqliteTables($connection);

            return;
        }

        $this->dropServerTables($connection, $driver);
    }

    private function dropSqliteTables(string $connection): void
    {
        try {
            $tables = DB::connection($connection)->select(
                "SELECT name FROM sqlite_master WHERE type='table' AND name != 'sqlite_sequence'"
            );
            foreach ($tables as $table) {
                DB::connection($connection)->statement('DROP TABLE IF EXISTS '.$table->name);
            }
            DB::connection($connection)->statement('DROP TABLE IF EXISTS migrations');
        } catch (\Exception $e) {
            // Continuar si hay error
        }
    }

    private function dropServerTables(string $connection, string $driver): void
    {
        try {
            if ($driver === 'mysql') {
                DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=0');
            }

            $tables = DB::connection($connection)->select('SHOW TABLES');
            $tableKey = 'Tables_in_'.config("database.connections.{$connection}.database");

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                DB::connection($connection)->statement("DROP TABLE IF EXISTS `{$tableName}`");
            }

            if ($driver === 'mysql') {
                DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=1');
            }
        } catch (\Exception $e) {
            try {
                $this->artisan('migrate:reset', ['--force' => true]);
            } catch (\Exception $e2) {
                // Continuar si no hay migraciones
            }
        }
    }

    private function runMigrationBatches(string $connection): void
    {
        $batches = [
            'batch_01_sistema_base',
            'batch_02_permisos',
            'batch_03_parametros',
            'batch_04_ubicaciones',
            'batch_05_personas',
            'batch_06_infraestructura',
            'batch_07_programas',
            'batch_08_fichas',
            'batch_09_instructores_aprendices',
            'batch_10_relaciones',
            'batch_11_jornadas_horarios',
            'batch_12_asistencias',
            'batch_13_competencias',
            'batch_14_evidencias',
            'batch_15_logs_auditoria',
            'batch_16_inventario',
            'batch_17_complementarios',
            'batch_18_entrada_salida',
        ];

        foreach ($batches as $batch) {
            $path = "database/migrations/{$batch}";
            if (! is_dir(base_path($path))) {
                continue;
            }

            $result = $this->artisan('migrate', [
                '--path' => $path,
                '--force' => true,
                '--database' => $connection,
            ]);

            if ($result !== 0) {
                throw new MigrationBatchException($batch);
            }
        }
    }

    /**
     * Reduce "database is locked" en SQLite (especialmente con seeders pesados).
     */
    private function configureSqliteConnection(string $connection, string $driver): void
    {
        if ($driver !== 'sqlite') {
            return;
        }

        try {
            DB::connection($connection)->statement('PRAGMA busy_timeout = 30000');
            DB::connection($connection)->statement('PRAGMA synchronous = NORMAL');
        } catch (\Exception $e) {
            // Ignorar si la conexión aún no admite PRAGMA
        }
    }
}
