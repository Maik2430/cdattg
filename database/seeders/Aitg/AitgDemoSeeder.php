<?php

namespace Database\Seeders\Aitg;

use Database\Seeders\Aitg\Plans\AitgPlanAgropecuarioDemoSeeder;
use Database\Seeders\Aitg\Plans\AitgPlanAutomotrizDemoSeeder;
use Database\Seeders\Aitg\Plans\AitgPlanGastronomiaDemoSeeder;
use Illuminate\Database\Seeder;

/** Seeder principal de datos demo del módulo AITG (3 formas de registro). */
class AitgDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('🌱 Creando datos demo AITG...');

        $this->call([
            AitgCompetenciasDemoSeeder::class,
            AitgPlanGastronomiaDemoSeeder::class,
            AitgPlanAutomotrizDemoSeeder::class,
            AitgPlanAgropecuarioDemoSeeder::class,
        ]);

        $this->command?->info('✅ Datos demo AITG listos.');
    }
}
