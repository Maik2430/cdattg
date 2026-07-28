<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AsistenciaConsultaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\ParametroSeeder::class,
            \Database\Seeders\PaisSeeder::class,
            \Database\Seeders\DepartamentoSeeder::class,
            \Database\Seeders\MunicipioSeeder::class,
        ]);
    }

    #[Test]
    public function requiere_autenticacion(): void
    {
        $instructorFicha = InstructorFichaCaracterizacion::factory()->create();
        $asistencia = Asistencia::create([
            'instructor_ficha_id' => $instructorFicha->id,
            'fecha' => now()->toDateString(),
            'hora_inicio' => now(),
            'is_finished' => false,
        ]);

        $response = $this->get(route('asistencia.consulta.show', $asistencia));

        $response->assertRedirect();
    }

    #[Test]
    public function puede_ver_consulta_autenticado(): void
    {
        $user = User::factory()->create();
        $user->assignRole('SUPER ADMINISTRADOR');

        $instructorFicha = InstructorFichaCaracterizacion::factory()->create();
        $asistencia = Asistencia::create([
            'instructor_ficha_id' => $instructorFicha->id,
            'fecha' => now()->toDateString(),
            'hora_inicio' => now(),
            'is_finished' => false,
            'user_create_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('asistencia.consulta.show', $asistencia));

        $response->assertStatus(200);
    }
}
