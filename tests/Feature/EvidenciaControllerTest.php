<?php

namespace Tests\Feature;

use App\Http\Controllers\EvidenciaController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * EvidenciaController no tiene ruta registrada (legacy/API interna).
 * Se prueba invocando store() directamente.
 */
class EvidenciaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([\Database\Seeders\RolePermissionSeeder::class]);
    }

    #[Test]
    public function puede_crear_evidencia_via_store(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = Request::create('/evidencias', 'POST', [
            'nombre' => 'Evidencia de prueba',
            'caracterizacion_id' => 1,
            'ficha_id' => 1,
        ]);

        $response = (new EvidenciaController)->store($request);

        $this->assertTrue($response->getData(true)['success']);
        $this->assertDatabaseHas('evidencias', [
            'nombre' => 'Evidencia de prueba',
            'user_create_id' => $user->id,
        ]);
    }
}
