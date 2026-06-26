<?php

namespace Database\Seeders\Aitg;

use App\Models\Aitg\Banco\ArchivoTalento;
use App\Models\Aitg\Banco\PostulacionArchivo;
use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\Convocatoria\Convocatoria;
use App\Models\Aitg\Evaluacion\EvaluacionPostulacion;
use App\Models\Aitg\PlanContratacion;
use App\Models\Aitg\Postulacion\PostulacionChecklistItem;
use App\Models\Aitg\Postulacion\PostulacionPuntoItem;
use App\Models\User;
use Database\Seeders\Aitg\Support\AitgFixtureHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Limpia postulaciones demo y deja una convocatoria nueva para probar la planilla.
 *
 * php artisan db:seed --class=Database\\Seeders\\Aitg\\AitgDemoPlanillaSeeder --force
 */
class AitgDemoPlanillaSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('🧹 AITG — Limpiando datos de prueba de postulaciones...');

        $this->limpiarPostulacionesDemo();

        $this->call([
            AitgBancoInstructoresSeeder::class,
            Plans\AitgPlanGastronomiaDemoSeeder::class,
        ]);

        $this->actualizarPlanGastronomia();
        $this->crearConvocatoriaDemo();

        $this->command?->info('');
        $this->command?->info('✅ Demo planilla lista.');
        $this->command?->info('   Competencia: GESTIÓN EN PROCESOS GASTRONÓMICOS');
        $this->command?->info('   Banco:       /aitg/banco-instructores → buscar GASTRON → elegir alternativa → subir PDFs');
        $this->command?->info('   Convocatoria: /aitg/convocatorias/publicas → CONV-GASTRO-PLANILLA-2026');
        $this->command?->info('   Login: superadmin@dataguaviare.com / Guaviare25.');
    }

    private function limpiarPostulacionesDemo(): void
    {
        DB::transaction(function () {
            if (Schema::hasTable('aitg_evaluacion_checklist_respuestas')) {
                DB::table('aitg_evaluacion_checklist_respuestas')->delete();
            }
            if (Schema::hasTable('aitg_evaluacion_puntos_respuestas')) {
                DB::table('aitg_evaluacion_puntos_respuestas')->delete();
            }

            EvaluacionPostulacion::query()->delete();
            PostulacionChecklistItem::query()->delete();
            PostulacionPuntoItem::query()->delete();

            if (Schema::hasTable('aitg_validaciones_documento')) {
                DB::table('aitg_validaciones_documento')->delete();
            }

            $archivoIds = PostulacionArchivo::query()->pluck('archivo_talento_id');
            PostulacionArchivo::query()->delete();

            ArchivoTalento::whereIn('id', $archivoIds)->delete();

            PostulacionPlan::query()->delete();

            Convocatoria::query()->delete();
        });

        $this->command?->info('  ✓ Postulaciones, convocatorias y archivos demo eliminados.');
    }

    private function actualizarPlanGastronomia(): void
    {
        $plan = PlanContratacion::where('observaciones', 'Demo Anexo 2 - Alternativas Gastronomía y Hotelería.')
            ->with('perfiles')
            ->first();

        if (! $plan) {
            $this->command?->warn('  ⚠ Plan Gastronomía demo no encontrado.');

            return;
        }

        $fx = new AitgFixtureHelper();
        $fx->sincronizarChecklistPlan($plan, $fx->checklistDemoEstandar(), true);

        foreach ($plan->perfiles as $perfil) {
            $perfil->update([
                'requiere_documento' => true,
                'documento_nombre' => 'Certificación del perfil',
                'documento_descripcion' => 'Suba el PDF (título, diploma o certificación) que acredite la alternativa seleccionada.',
                'documento_es_obligatorio' => false,
            ]);
        }

        $this->command?->info('  ✓ Plan Gastronomía actualizado (alternativas + checklist planilla).');
    }

    private function crearConvocatoriaDemo(): void
    {
        $plan = PlanContratacion::where('observaciones', 'Demo Anexo 2 - Alternativas Gastronomía y Hotelería.')
            ->with('competencia')
            ->firstOrFail();

        $admin = User::where('email', 'superadmin@dataguaviare.com')->first();

        Convocatoria::create([
            'codigo' => 'CONV-GASTRO-PLANILLA-2026',
            'titulo' => 'Instructor Gestión en Procesos Gastronómicos — Demo Planilla',
            'competencia_id' => $plan->competencia_id,
            'plan_contratacion_id' => $plan->id,
            'descripcion' => 'Convocatoria de demostración: complete el banco (alternativa + checklist) y luego postule aquí.',
            'objeto_contractual' => 'Prestar servicios como instructor en Gestión en Procesos Gastronómicos.',
            'requisitos' => 'Perfil del plan acreditado en Banco de Talento y checklist documental completo.',
            'estado' => 'publicada',
            'codigo_cdp' => 'CDP-DEMO-PLANILLA',
            'valor_total' => 48000000,
            'valor_contrato_honorarios' => 4200000,
            'fecha_inicio_publicacion' => now()->subDay(),
            'fecha_fin_publicacion' => now()->addMonths(3),
            'fecha_inicio_contrato' => now()->addMonth(),
            'fecha_fin_contrato' => now()->addMonths(8),
            'regional_id' => $plan->regional_id,
            'fecha_publicacion' => now(),
            'user_create_id' => $admin?->id,
            'user_update_id' => $admin?->id,
        ]);

        $this->command?->info('  ✓ Convocatoria CONV-GASTRO-PLANILLA-2026 creada.');
    }
}
