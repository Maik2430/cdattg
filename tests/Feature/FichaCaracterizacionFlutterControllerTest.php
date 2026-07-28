<?php

namespace Tests\Feature;

use App\Models\FichaCaracterizacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FichaCaracterizacionFlutterControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);

        User::factory()->create();
    }

    #[Test]
    public function puede_listar_todas_las_fichas(): void
    {
        FichaCaracterizacion::factory()->count(2)->create();

        $response = $this->getJson('/api/fichas-caracterizacion/flutter/all');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'total' => 2,
        ]);
        $response->assertJsonStructure([
            'success',
            'data',
            'total',
        ]);
    }

    #[Test]
    public function retorna_404_si_ficha_no_existe(): void
    {
        $response = $this->getJson('/api/fichas-caracterizacion/flutter/999999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Ficha de caracterización no encontrada',
        ]);
    }

    #[Test]
    public function puede_obtener_ficha_por_id(): void
    {
        $ficha = FichaCaracterizacion::factory()->create();

        $response = $this->getJson('/api/fichas-caracterizacion/flutter/'.$ficha->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $ficha->id,
                'ficha' => $ficha->ficha,
            ],
        ]);
    }

    #[Test]
    public function puede_buscar_fichas_por_numero(): void
    {
        $ficha = FichaCaracterizacion::factory()->create(['ficha' => '987654']);

        $response = $this->postJson('/api/fichas-caracterizacion/flutter/search', [
            'numero' => '987654',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'total' => 1,
        ]);
        $response->assertJsonPath('data.0.ficha', $ficha->ficha);
    }

    #[Test]
    public function puede_obtener_cantidad_de_aprendices_por_ficha(): void
    {
        $ficha = FichaCaracterizacion::factory()->create();

        $response = $this->getJson('/api/fichas-caracterizacion/flutter/aprendices/'.$ficha->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'ficha_id' => (string) $ficha->id,
            'cantidad_aprendices' => 0,
        ]);
    }
}
