<?php

namespace Database\Seeders\Aitg\Plans;

use Database\Seeders\Aitg\Support\AitgFixtureHelper;
use Illuminate\Database\Seeder;

/** Plan demo AITG: registro por opciones (Mecatrónica Automotriz). */
class AitgPlanAutomotrizDemoSeeder extends Seeder
{
    public function run(): void
    {
        $fx = new AitgFixtureHelper();

        $programaPrincipal = $fx->programa(
            'MECATRONICA AUTOMOTRIZ',
            'TECNÓLOGO',
            '228701'
        );

        $fx->crearPlan(
            [
                'programa_formacion_id' => $programaPrincipal->id,
                'tipo_registro_perfil' => 'opcion',
                'modalidad' => 'regular',
                'periodo' => '2026-1',
                'fecha_inicio' => '2026-01-01',
                'fecha_fin' => '2026-12-31',
                'estado' => 'activo',
                'observaciones' => 'Demo Anexo 2 - Opciones Mecatrónica Automotriz.',
            ],
            [
                $fx->perfil(
                    'OPCIÓN 1 TÉCNICOS: Área ocupacional 83, grupo 838, subgrupo 8381 - Mecánico vehículos automotores.',
                    true, 30, 12
                ),
                $fx->perfil(
                    'OPCIÓN 2 TÉCNICOS PROFESIONALES: Técnica profesional automotriz, ingeniería automotriz, mecánica automotriz o servicio automotriz.',
                    true, 24, 12
                ),
                $fx->perfil(
                    'OPCIÓN 3 TECNÓLOGOS: Tecnología en diagnóstico y gestión automotriz, mecánica automotriz, mecatrónica o autotrónica.',
                    true, 18, 12
                ),
                $fx->perfil(
                    'OPCIÓN 4 PROFESIONAL UNIVERSITARIO: Ingeniería automotriz o ingeniería en mecatrónica.',
                    true, 12, 12
                ),
            ],
            [
                ['descripcion' => 'Experiencia certificada en mecatrónica automotriz con soporte documental', 'puntaje_adicional' => 3],
            ]
        );

        $this->command?->info('✓ Plan demo Automotriz (opciones) creado.');
    }
}
