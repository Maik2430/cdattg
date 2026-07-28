<?php

namespace Tests\Feature\Livewire\Competencias;

use App\Livewire\Competencias\GestionarResultados;
use App\Models\Competencia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GestionarResultadosTest extends TestCase
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
    public function puede_montar_el_componente(): void
    {
        $competencia = Competencia::create([
            'codigo' => 'COMP-SMOKE-001',
            'nombre' => 'Competencia Smoke Test',
            'descripcion' => 'Descripción para smoke test de Livewire',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(GestionarResultados::class, ['competencia' => $competencia])
            ->assertStatus(200);
    }
}
