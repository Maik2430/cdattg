<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->truncateGeneratedData();
        $this->call([
            RolePermissionSeeder::class,
            ParametroSeeder::class,
            TemaSeeder::class,
            PaisSeeder::class,
            DepartamentoSeeder::class,
            MunicipioSeeder::class,
            PersonaSeeder::class,
            UsersSeeder::class,
            RegionalSeeder::class,
            CentroFormacionSeeder::class,
            SedeSeeder::class,
            BloqueSeeder::class,
            PisoSeeder::class,
            AmbienteSeeder::class,
            ProductoSeeder::class, // Crear productos para agregar al módulo de inventario
            // Complementarios ofertados
            // ComplementariosOfertadosSeeder::class,
            // Aspirantes complementarios
            // AspirantesComplementariosSeeder::class,
            // Categorias de caracterizacion para complementarios
            // CategoriaCaracterizacionComplementariosSeeder::class,
        ]);

    }
    private function truncateGeneratedData(): void
    {
        $tables = [
            'instructor_ficha_dias',
            'instructor_fichas_caracterizacion',
            'ficha_dias_formacion',
            'fichas_caracterizacion',
            'aprendiz_fichas_caracterizacion',
            'aprendices',
            'proveedores',
            'contratos_convenios',
            'productos',
            'detalle_ordenes',
            'aprobaciones',
            'ordenes',
            'complementarios_ofertados_dias_formacion',
            'aspirantes_complementarios',
            'complementarios_ofertados',
        ];
        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();
    }
}

