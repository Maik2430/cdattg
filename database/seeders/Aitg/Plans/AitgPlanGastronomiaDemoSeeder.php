<?php

namespace Database\Seeders\Aitg\Plans;

use Database\Seeders\Aitg\Support\AitgFixtureHelper;
use Illuminate\Database\Seeder;

/** Plan demo AITG: registro por alternativas (Gastronomía). */
class AitgPlanGastronomiaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $fx = new AitgFixtureHelper();

        $competencia = $fx->competencia('GESTIÓN EN PROCESOS GASTRONÓMICOS', '240201');

        if ($fx->planDemoExiste('Demo Anexo 2 - Alternativas Gastronomía y Hotelería.')) {
            $this->command?->info('→ Plan demo Gastronomía ya existe, se omite.');

            return;
        }

        $fx->crearPlan(
            [
                'competencia_id' => $competencia->id,
                'tipo_registro_perfil' => 'alternativa',
                'modalidad' => 'regular',
                'periodo' => '2026-1',
                'fecha_inicio' => '2026-01-01',
                'fecha_fin' => '2026-12-31',
                'estado' => 'activo',
                'observaciones' => 'Demo Anexo 2 - Alternativas Gastronomía y Hotelería.',
            ],
            [
                $fx->perfil(
                    'ALTERNATIVA 1: Profesional en las áreas de gastronomía, cocina, alta cocina, culinaria o gestión hotelera.',
                    true, 24, 12
                ),
                $fx->perfil(
                    'ALTERNATIVA 2: Tecnólogo en las áreas de cocina, gastronomía, culinaria, gestión hotelera o gestión de alimentos y bebidas.',
                    true, 24, 12
                ),
                $fx->perfil(
                    'ALTERNATIVA 3: Técnico profesional en las áreas de cocina, gastronomía o culinaria.',
                    true, 24, 12
                ),
                $fx->perfil(
                    'ALTERNATIVA 4: Técnico en las áreas de cocina, gastronomía o culinaria.',
                    true, 24, 12
                ),
            ],
            [
                ['descripcion' => 'Curso de Pedagogía (40 horas)', 'puntaje_adicional' => 5],
                ['descripcion' => 'Certificación Internacional de Inglés', 'puntaje_adicional' => 10],
            ],
            $fx->checklistDemoEstandar()
        );

        $this->command?->info('✓ Plan demo Gastronomía (alternativas) creado.');
    }
}
