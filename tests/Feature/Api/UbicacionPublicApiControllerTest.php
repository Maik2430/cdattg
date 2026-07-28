<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UbicacionPublicApiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\PaisSeeder::class,
        ]);
    }

    #[Test]
    public function puede_obtener_paises_activos(): void
    {
        $response = $this->getJson(route('api.paises'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'paises']);
        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function endpoint_paises_es_publico(): void
    {
        $response = $this->getJson('/api/paises');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
    }
}
