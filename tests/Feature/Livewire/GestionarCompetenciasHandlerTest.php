<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;

use App\Livewire\ResultadosAprendizaje\GestionarCompetenciasHandler;
use App\Models\ResultadosAprendizaje;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class GestionarCompetenciasHandlerTest extends TestCase
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
    public function puede_montar_gestionar_competencias_handler(): void
    {
        $user = User::factory()->create();

        $resultado = ResultadosAprendizaje::factory()->create([
            'codigo' => 'RAP-SMOKE-GCH-001',
            'nombre' => 'RAP smoke gestionar competencias handler',
            'status' => true,
        ]);

        Livewire::actingAs($user)
            ->test(GestionarCompetenciasHandler::class, ['resultadoId' => $resultado->id])
            ->assertStatus(200);
    }
}
