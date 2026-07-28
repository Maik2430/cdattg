<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;

use App\Livewire\GuiasAprendizaje\GuiaAprendizajeIndex;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class GuiaAprendizajeIndexTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RolePermissionSeeder::class]);

        $this->user = User::factory()->create();
        $this->user->assignRole('SUPER ADMINISTRADOR');
    }

    #[Test]
    public function can_render(): void
    {
        Livewire::actingAs($this->user)
            ->test(GuiaAprendizajeIndex::class)
            ->assertStatus(200);
    }

    #[Test]
    public function puede_eliminar_guia_sin_actividades(): void
    {
        $guia = \App\Models\GuiasAprendizaje::factory()->create([
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(GuiaAprendizajeIndex::class)
            ->call('deleteGuia', $guia->id)
            ->assertDispatched('notify');

        $this->assertSoftDeleted('guia_aprendizajes', ['id' => $guia->id]);
    }
}
