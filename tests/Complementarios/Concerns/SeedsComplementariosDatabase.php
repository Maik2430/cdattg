<?php

declare(strict_types=1);

namespace Tests\Complementarios\Concerns;

use App\Models\Parametro;
use Illuminate\Support\Facades\Schema;

/**
 * Trait para optimizar el seeding de la base de datos en tests de Complementarios.
 * Solo ejecuta los seeders si los datos base no existen, mejorando significativamente
 * el rendimiento de los tests.
 * IMPORTANTE: Este trait funciona con RefreshDatabase. Verifica si las tablas existen
 * y tienen datos antes de ejecutar los seeders, evitando ejecuciones innecesarias.
 */
trait SeedsComplementariosDatabase
{
    /**
     * Ejecuta los seeders necesarios solo si los datos base no existen.
     * Esto evita re-ejecutar seeders costosos en cada test.
     */
    protected function seedComplementariosDatabaseIfNeeded(): void
    {
        // En cualquier entorno que no sea producción, verificar si los datos base existen
        // Si no existen o están incompletos, ejecutar seeders
        if (app()->environment('production')) {
            // En producción, solo ejecutar si no hay datos
            try {
                if (Schema::hasTable('parametros') && \App\Models\Parametro::count() > 0) {
                    return;
                }
            } catch (\Exception $e) {
                // Si hay error, ejecutar seeders
            }
        } else {
            // En desarrollo/testing, verificar datos críticos
            try {
                // Verificar si tema_id=3 existe (GENERO)
                $temaGeneroExists = Schema::hasTable('temas') && 
                    \App\Models\Tema::where('id', 3)->exists();
                
                // Verificar si hay parametros_temas para genero
                $parametroTemaExists = Schema::hasTable('parametros_temas') &&
                    \App\Models\ParametroTema::where('tema_id', 3)
                        ->whereIn('parametro_id', [9, 10, 11])
                        ->exists();
                
                if ($temaGeneroExists && $parametroTemaExists) {
                    // Datos críticos existen, no ejecutar seeders
                    return;
                }
            } catch (\Exception $e) {
                // Si hay error, ejecutar seeders
            }
        }

        // Ejecutar seeders
        $this->runSeeders();
    }

    /**
     * Ejecuta todos los seeders necesarios.
     */
    private function runSeeders(): void
    {
        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\ParametroSeeder::class,
            \Database\Seeders\TemaSeeder::class,
            \Database\Seeders\PaisSeeder::class,
            \Database\Seeders\DepartamentoSeeder::class,
            \Database\Seeders\MunicipioSeeder::class,
            \Database\Seeders\PersonaSeeder::class,
            \Database\Seeders\UsersSeeder::class,
            \Database\Seeders\RegionalSeeder::class,
            \Database\Seeders\CentroFormacionSeeder::class,
            \Database\Seeders\SedeSeeder::class,
            \Database\Seeders\BloqueSeeder::class,
            \Database\Seeders\PisoSeeder::class,
            \Database\Seeders\AmbienteSeeder::class,
            \Database\Seeders\JornadaFormacionSeeder::class,
        ]);
    }
}
