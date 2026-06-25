<?php

namespace Database\Seeders\Aitg\Plans;

use Database\Seeders\Aitg\Support\AitgFixtureHelper;
use Illuminate\Database\Seeder;

/** Plan demo AITG: registro directo por nivel y programa (Agropecuario). */
class AitgPlanAgropecuarioDemoSeeder extends Seeder
{
    public function run(): void
    {
        $fx = new AitgFixtureHelper();

        $competencia = $fx->competencia('INGLES', '240801');

        if ($fx->planDemoExiste('Demo Anexo 2 - Registro directo Agropecuario.')) {
            $this->command?->info('→ Plan demo Agropecuario ya existe, se omite.');

            return;
        }

        $fx->crearPlan(
            [
                'competencia_id' => $competencia->id,
                'tipo_registro_perfil' => 'directo',
                'modalidad' => 'regular',
                'periodo' => '2026-1',
                'fecha_inicio' => '2026-01-01',
                'fecha_fin' => '2026-12-31',
                'estado' => 'borrador',
                'observaciones' => 'Demo Anexo 2 - Registro directo Agropecuario.',
            ],
            [
                $fx->perfil(
                    'Ingeniero agrónomo',
                    true, 6, 6,
                    'Programa académico en ingeniería agronómica o áreas afines'
                ),
                $fx->perfil(
                    'Administrador de empresas agropecuarias',
                    true, 6, 6,
                    'Formación en administración de empresas agropecuarias'
                ),
                $fx->perfil(
                    'Tecnólogo en administración de empresas agropecuarias',
                    true, 6, 6,
                    'Tecnólogo en administración de empresas agropecuarias y áreas afines'
                ),
            ],
            []
        );

        $this->command?->info('✓ Plan demo Agropecuario (directo) creado.');
    }
}
