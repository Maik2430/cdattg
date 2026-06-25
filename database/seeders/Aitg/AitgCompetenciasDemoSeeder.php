<?php

namespace Database\Seeders\Aitg;

use Database\Seeders\Aitg\Support\AitgFixtureHelper;
use Illuminate\Database\Seeder;

/** Catálogo de competencias demo para formularios AITG. */
class AitgCompetenciasDemoSeeder extends Seeder
{
    public function run(): void
    {
        $fx = new AitgFixtureHelper();

        $catalogo = [
            ['INGLES', '240801'],
            ['COMUNICACIÓN EN INGLÉS', '240802'],
            ['GESTIÓN EN PROCESOS GASTRONÓMICOS', '240201'],
            ['DIAGNÓSTICO DE SISTEMAS AUTOMOTRICES', '228701'],
            ['GESTIÓN DE PROCESOS AGROPECUARIOS', '228801'],
            ['APLICACIÓN DE CONOCIMIENTOS DE LAS CIENCIAS NATURALES', '240101'],
        ];

        foreach ($catalogo as [$nombre, $codigo]) {
            $fx->competencia($nombre, $codigo);
        }

        $fx->repararPlanesSinCompetencia();

        $this->command?->info('✓ Competencias demo AITG listas.');
    }
}
