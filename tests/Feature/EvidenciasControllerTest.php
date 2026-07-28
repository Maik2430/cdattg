<?php

namespace Tests\Feature;

use App\Models\InstructorFichaCaracterizacion;
use App\Models\ResultadosAprendizaje;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EvidenciasControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);

        $this->user = User::factory()->create();
    }

    #[Test]
    public function puede_ver_formulario_crear_evidencia(): void
    {
        $this->actingAs($this->user);

        $caracterizacion = InstructorFichaCaracterizacion::factory()->create();
        $rap = ResultadosAprendizaje::factory()->create([
            'codigo' => 'RAP-EVID-001',
            'nombre' => 'Resultado evidencia prueba',
            'status' => true,
        ]);
        $caracterizacion->resultadosAprendizaje()->attach($rap->id);

        $response = $this->get(route('evidencias.create', [
            'caracterizacion' => $caracterizacion->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('registro_actividades.create');
    }

    #[Test]
    public function requiere_autenticacion(): void
    {
        $caracterizacion = InstructorFichaCaracterizacion::factory()->create();

        $response = $this->get(route('evidencias.create', [
            'caracterizacion' => $caracterizacion->id,
        ]));

        $response->assertRedirect(route('verificarLogin'));
    }
}
