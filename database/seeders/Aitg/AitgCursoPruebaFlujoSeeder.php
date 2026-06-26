<?php

namespace Database\Seeders\Aitg;

use App\Models\Aitg\Convocatoria\Convocatoria;
use App\Models\Aitg\PlanContratacion;
use App\Models\User;
use Database\Seeders\Aitg\Support\AitgDemoPostulacionFactory;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * Curso de prueba AITG: alimenta validación, evaluación y selección con datos listos.
 *
 * Ejecutar:
 *   php artisan db:seed --class=Database\\Seeders\\Aitg\\AitgCursoPruebaFlujoSeeder
 */
class AitgCursoPruebaFlujoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('🎓 AITG — Curso de prueba (validación → evaluación → selección)...');

        $this->call(AitgFlujoPruebaSeeder::class);

        $admin = User::where('email', 'superadmin@dataguaviare.com')->firstOrFail();
        $convocatoria = Convocatoria::where('codigo', 'CONV-GASTRONOMIA-2026')->with('plan.perfiles')->firstOrFail();
        $plan = $convocatoria->plan;
        $perfil = $plan->perfiles->first();

        if (! $perfil) {
            $this->command?->error('El plan demo no tiene perfiles. Ejecute AitgPlanGastronomiaDemoSeeder.');

            return;
        }

        $aspiranteRole = Role::firstOrCreate(['name' => 'ASPIRANTE INSTRUCTOR', 'guard_name' => 'web']);

        $aspirantes = [
            ['email' => 'instructor@dataguaviare.com', 'escenario' => 'pendiente_revision'],
            ['email' => 'aprendiz1@dataguaviare.com', 'escenario' => 'preseleccionado'],
            ['email' => 'aprendiz2@dataguaviare.com', 'escenario' => 'evaluacion_aprobada'],
            ['email' => 'coordinador@dataguaviare.com', 'escenario' => 'evaluacion_aprobada_alto'],
        ];

        /** @var AitgDemoPostulacionFactory $factory */
        $factory = app(AitgDemoPostulacionFactory::class);

        foreach ($aspirantes as $row) {
            $user = User::where('email', $row['email'])->first();
            if (! $user) {
                $this->command?->warn("  ⚠ Usuario {$row['email']} no existe — omitido.");

                continue;
            }

            if (! $user->hasRole($aspiranteRole)) {
                $user->assignRole($aspiranteRole);
            }

            $factory->asegurarBancoAprobado($user, $plan->id, $admin->id);

            $postulacion = $factory->crearPostulacionConvocatoria(
                $user,
                $convocatoria->id,
                $plan->id,
                $perfil->id,
                $admin->id
            );

            $postulacion = $factory->cargarChecklistYEnviar($postulacion, $user);

            match ($row['escenario']) {
                'pendiente_revision' => null,
                'preseleccionado' => $factory->validarDocumentos($postulacion, $admin),
                'evaluacion_aprobada' => $factory->evaluarYAprobar($postulacion, $admin),
                'evaluacion_aprobada_alto' => $factory->evaluarConPorcentajeChecklist($postulacion, $admin, 0.5),
                default => null,
            };

            $postulacion->refresh();
            $this->command?->info("  ✓ {$row['email']} → estado: {$postulacion->estado}");
        }

        $this->command?->info('');
        $this->command?->info('✅ Curso de prueba listo — CONV-GASTRONOMIA-2026');
        $this->command?->info('');
        $this->command?->info('  1. Validar solicitudes → postulación instructor@ (pendiente_revision, checklist con PDFs)');
        $this->command?->info('  2. Evaluación documental → aprendiz1@ (preseleccionado)');
        $this->command?->info('  3. Selección instructor → aprendiz2@ y coordinador@ (evaluacion_aprobada)');
        $this->command?->info('');
        $this->command?->info('  Admin/validador: superadmin@dataguaviare.com / Guaviare25.');
        $this->command?->info('  Aspirantes demo: misma clave según UsersSeeder (Guaviare25. o Guaviare25!)');
    }
}
