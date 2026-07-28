<?php

namespace Tests\Feature\Livewire\Competencias;

use App\Livewire\Competencias\CompetenciaIndex;
use App\Models\Competencia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompetenciaIndexTest extends TestCase
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

    private function crearCompetencia(array $overrides = []): Competencia
    {
        return Competencia::query()->create(array_merge([
            'codigo' => 'COMP-LW-'.uniqid(),
            'nombre' => 'Competencia Livewire Test',
            'descripcion' => 'Descripción de prueba',
            'duracion' => 40,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addMonths(3)->toDateString(),
            'status' => true,
            'user_create_id' => $this->user->id,
        ], $overrides));
    }

    #[Test]
    public function puede_montar_el_componente(): void
    {
        Livewire::actingAs($this->user)
            ->test(CompetenciaIndex::class)
            ->assertStatus(200);
    }

    #[Test]
    public function puede_cambiar_estado_de_competencia(): void
    {
        $competencia = $this->crearCompetencia(['status' => true]);

        Livewire::actingAs($this->user)
            ->test(CompetenciaIndex::class)
            ->call('toggleStatus', $competencia->id)
            ->assertDispatched('notify');

        $this->assertFalse((bool) $competencia->fresh()->status);
    }

    #[Test]
    public function puede_eliminar_competencia_sin_programas(): void
    {
        $competencia = $this->crearCompetencia();

        Livewire::actingAs($this->user)
            ->test(CompetenciaIndex::class)
            ->call('deleteCompetencia', $competencia->id)
            ->assertDispatched('notify');

        $this->assertDatabaseMissing('competencias', ['id' => $competencia->id]);
    }
}
