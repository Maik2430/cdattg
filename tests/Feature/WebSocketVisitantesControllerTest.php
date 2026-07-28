<?php

namespace Tests\Feature;

use App\Services\EstadisticasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebSocketVisitantesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function puede_obtener_visitantes_actuales(): void
    {
        $response = $this->getJson('/api/websocket/visitantes-actuales');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [],
        ]);
    }

    #[Test]
    public function puede_obtener_estadisticas(): void
    {
        $this->mock(EstadisticasService::class, function ($mock) {
            $mock->shouldReceive('obtenerDashboardGeneral')
                ->once()
                ->andReturn(['roles' => []]);
        });

        $response = $this->getJson('/api/websocket/estadisticas');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => ['roles' => []],
        ]);
    }
}
