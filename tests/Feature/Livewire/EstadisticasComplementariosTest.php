<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;

use App\Livewire\Complementarios\EstadisticasComplementarios;
use App\Models\User;
use App\Services\Complementarios\EstadisticaComplementarioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class EstadisticasComplementariosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);
    }

    #[Test]
    public function puede_montar_estadisticas_complementarios(): void
    {
        $this->mock(EstadisticaComplementarioService::class, function ($mock): void {
            $mock->shouldReceive('obtenerEstadisticasReales')->andReturn([
                'total_aspirantes' => 0,
                'aspirantes_aceptados' => 0,
                'aspirantes_pendientes' => 0,
                'programas_activos' => 0,
                'tendencia_inscripciones' => Collection::make([]),
                'distribucion_programas' => Collection::make([]),
                'programas_demanda' => Collection::make([]),
            ]);
        });

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(EstadisticasComplementarios::class)
            ->assertStatus(200);
    }
}
