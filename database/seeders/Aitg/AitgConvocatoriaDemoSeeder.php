<?php

namespace Database\Seeders\Aitg;

use App\Models\Aitg\Convocatoria\Convocatoria;
use App\Models\Aitg\PlanContratacion;
use App\Models\User;
use Illuminate\Database\Seeder;

class AitgConvocatoriaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $plan = PlanContratacion::with('competencia')->whereIn('estado', ['activo', 'borrador'])->first();

        if (! $plan) {
            $this->command?->warn('No hay plan AITG para crear convocatoria demo.');

            return;
        }

        $admin = User::where('email', 'superadmin@dataguaviare.com')->first();

        Convocatoria::updateOrCreate(
            ['codigo' => 'CONV-' . now()->format('Y') . '-001'],
            [
                'titulo' => 'Instructor – ' . ($plan->competencia->nombre ?? 'Competencia demo') . ' – ' . now()->format('Y'),
                'competencia_id' => $plan->competencia_id,
                'plan_contratacion_id' => $plan->id,
                'descripcion' => 'Convocatoria demo para contratación de instructor SENA.',
                'objeto_contractual' => 'Prestar servicios profesionales como instructor en la competencia asociada.',
                'requisitos' => 'Formación académica acorde al perfil, experiencia relacionada y antecedentes al día.',
                'estado' => 'publicada',
                'codigo_cdp' => 'CDP-DEMO-001',
                'valor_total' => 50000000,
                'valor_contrato_honorarios' => 4500000,
                'fecha_inicio_publicacion' => now()->startOfMonth(),
                'fecha_fin_publicacion' => now()->addMonths(2)->endOfMonth(),
                'fecha_inicio_contrato' => now()->addMonth(),
                'fecha_fin_contrato' => now()->addMonths(7),
                'regional_id' => $plan->regional_id,
                'fecha_publicacion' => now(),
                'user_create_id' => $admin?->id,
                'user_update_id' => $admin?->id,
            ]
        );

        $this->command?->info('✓ Convocatoria demo AITG creada.');
    }
}
