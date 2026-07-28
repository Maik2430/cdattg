<?php

namespace Tests\Feature\Api;

use App\Models\Competencia;
use App\Models\GuiasAprendizaje;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ComplementarioApiControllerTest extends TestCase
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
    public function get_competencias_retorna_json(): void
    {
        $user = User::factory()->create();
        Competencia::query()->create([
            'codigo' => 'COMP-API-001',
            'nombre' => 'Competencia API Test',
            'descripcion' => 'Descripción',
            'duracion' => 40,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addMonths(3)->toDateString(),
            'status' => true,
            'user_create_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/complementarios/competencias');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'codigo', 'nombre'],
        ]);
    }

    #[Test]
    public function get_raps_sin_competencias_retorna_vacio(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/complementarios/raps');

        $response->assertStatus(200);
        $response->assertExactJson([]);
    }

    #[Test]
    public function get_guias_aprendizaje_retorna_json(): void
    {
        $user = User::factory()->create();
        GuiasAprendizaje::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/complementarios/guias-aprendizaje');

        $response->assertStatus(200);
    }
}
