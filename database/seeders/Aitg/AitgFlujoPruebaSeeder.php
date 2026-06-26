<?php

namespace Database\Seeders\Aitg;

use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\Convocatoria\Convocatoria;
use App\Models\Aitg\PlanContratacion;
use App\Models\Aitg\PuntoAdicional;
use App\Models\User;
use App\Services\Aitg\Postulacion\AitgPostulacionItemsService;
use Database\Seeders\Aitg\Support\AitgFixtureHelper;
use Illuminate\Database\Seeder;

/**
 * Datos de prueba end-to-end: plan con checklist, convocatoria publicada e ítems en postulaciones.
 * Ejecutar: php artisan db:seed --class=Database\\Seeders\\Aitg\\AitgFlujoPruebaSeeder
 */
class AitgFlujoPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $fx = new AitgFixtureHelper();
        $itemsService = app(AitgPostulacionItemsService::class);

        $this->command?->info('🌱 AITG — datos de prueba flujo completo...');

        $this->call([
            AitgBancoInstructoresSeeder::class,
            \Database\Seeders\Aitg\Plans\AitgPlanGastronomiaDemoSeeder::class,
        ]);

        $checklist = $fx->checklistDemoEstandar();
        $puntosDemo = [
            ['descripcion' => 'Curso de Pedagogía (40 horas)', 'puntaje_adicional' => 5],
            ['descripcion' => 'Certificación Internacional de Inglés', 'puntaje_adicional' => 10],
        ];

        PlanContratacion::query()
            ->whereIn('estado', ['activo', 'borrador'])
            ->each(function (PlanContratacion $plan) use ($fx, $puntosDemo, $checklist) {
                $forzar = $plan->checklist()->count() > 0 && $plan->checklist()->count() < 4;
                $fx->sincronizarChecklistPlan($plan, $checklist, $forzar);

                if ($plan->puntosAdicionales()->count() === 0) {
                    foreach ($puntosDemo as $index => $punto) {
                        PuntoAdicional::create([
                            'plan_contratacion_id' => $plan->id,
                            'consecutivo' => $index + 1,
                            'descripcion' => $punto['descripcion'],
                            'puntaje_adicional' => $punto['puntaje_adicional'],
                            'orden' => $index + 1,
                        ]);
                    }
                }
            });

        $planGastro = PlanContratacion::where('observaciones', 'Demo Anexo 2 - Alternativas Gastronomía y Hotelería.')
            ->with('competencia')
            ->first();

        if (! $planGastro) {
            $planGastro = PlanContratacion::with('competencia')->whereIn('estado', ['activo', 'borrador'])->first();
        }

        if ($planGastro) {
            $admin = User::where('email', 'superadmin@dataguaviare.com')->first();

            Convocatoria::updateOrCreate(
                ['codigo' => 'CONV-GASTRONOMIA-2026'],
                [
                    'titulo' => 'Instructor Gastronomía 2026 — PRUEBA',
                    'competencia_id' => $planGastro->competencia_id,
                    'plan_contratacion_id' => $planGastro->id,
                    'descripcion' => 'Convocatoria de prueba con checklist documental completo.',
                    'objeto_contractual' => 'Prestar servicios como instructor en Gestión en Procesos Gastronómicos.',
                    'requisitos' => 'Perfil del plan, checklist documental y puntos adicionales opcionales.',
                    'estado' => 'publicada',
                    'codigo_cdp' => 'CDP-PRUEBA-2026',
                    'valor_total' => 48000000,
                    'valor_contrato_honorarios' => 4200000,
                    'fecha_inicio_publicacion' => now()->subDays(5),
                    'fecha_fin_publicacion' => now()->addMonths(2),
                    'fecha_inicio_contrato' => now()->addMonth(),
                    'fecha_fin_contrato' => now()->addMonths(8),
                    'regional_id' => $planGastro->regional_id,
                    'fecha_publicacion' => now(),
                    'user_create_id' => $admin?->id,
                    'user_update_id' => $admin?->id,
                ]
            );

            $this->command?->info('✓ Convocatoria CONV-GASTRONOMIA-2026 publicada (plan #' . $planGastro->id . ').');
        }

        PostulacionPlan::query()->each(function (PostulacionPlan $postulacion) use ($itemsService) {
            try {
                $itemsService->instanciarDesdePlan($postulacion->loadMissing('plan'));
            } catch (\Throwable) {
                // Plan sin checklist: se omite.
            }
        });

        $this->command?->info('');
        $this->command?->info('✅ Flujo de prueba listo.');
        $this->command?->info('   Banco:      /aitg/banco-instructores → buscar "GASTRONOM"');
        $this->command?->info('   Convocatoria: /aitg/convocatorias/publicas → "Instructor Gastronomía 2026"');
        $this->command?->info('   Login demo: superadmin@dataguaviare.com / Guaviare25.');
    }
}
