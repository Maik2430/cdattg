<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ResultadosAprendizaje\ResultadoAprendizajeIndex;
use App\Models\ResultadosAprendizaje;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ResultadoAprendizajeIndexTest extends TestCase
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
        $this->user->assignRole('SUPER ADMINISTRADOR');
    }

    #[Test]
    public function puede_montar_resultado_aprendizaje_index(): void
    {
        Livewire::actingAs($this->user)
            ->test(ResultadoAprendizajeIndex::class)
            ->assertStatus(200);
    }

    #[Test]
    public function puede_cambiar_estado_de_resultado(): void
    {
        $resultado = ResultadosAprendizaje::factory()->create([
            'status' => true,
            'user_create_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ResultadoAprendizajeIndex::class)
            ->call('toggleStatus', $resultado->id)
            ->assertDispatched('notify');

        $this->assertFalse((bool) $resultado->fresh()->status);
    }

    #[Test]
    public function puede_eliminar_resultado_sin_guias(): void
    {
        $resultado = ResultadosAprendizaje::factory()->create([
            'user_create_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(ResultadoAprendizajeIndex::class)
            ->call('deleteResultado', $resultado->id)
            ->assertDispatched('notify');

        $this->assertDatabaseMissing('resultados_aprendizajes', ['id' => $resultado->id]);
    }
}
