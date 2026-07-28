<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;

use App\Livewire\ResultadosAprendizaje\GestionarCompetencias;
use App\Models\ResultadosAprendizaje;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class GestionarCompetenciasTest extends TestCase
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
    public function puede_montar_gestionar_competencias(): void
    {
        $user = User::factory()->create();

        $resultado = ResultadosAprendizaje::factory()->create([
            'codigo' => 'RAP-SMOKE-GC-001',
            'nombre' => 'RAP smoke gestionar competencias',
            'status' => true,
        ]);

        Livewire::actingAs($user)
            ->test(GestionarCompetencias::class, ['resultadoId' => $resultado->id])
            ->assertStatus(200);
    }
}
