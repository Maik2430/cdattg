<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Programas\ProgramaIndex;
use App\Models\ProgramaFormacion;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProgramaIndexTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            \Database\Seeders\ParametroSeeder::class,
        ]);

        $this->user = User::factory()->create();
        $this->user->assignRole('SUPER ADMINISTRADOR');
    }

    #[Test]
    public function can_render(): void
    {
        Livewire::actingAs($this->user)
            ->test(ProgramaIndex::class)
            ->assertStatus(200);
    }

    #[Test]
    public function puede_cambiar_estado_de_programa(): void
    {
        $programa = ProgramaFormacion::factory()->create([
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);
        $programa->competencias()->detach();

        Livewire::actingAs($this->user)
            ->test(ProgramaIndex::class)
            ->call('toggleStatus', $programa->id)
            ->assertDispatched('notify');

        $this->assertFalse((bool) $programa->fresh()->status);
    }

    #[Test]
    public function puede_eliminar_programa_sin_relaciones(): void
    {
        $programa = ProgramaFormacion::factory()->create([
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);
        $programa->competencias()->detach();

        Livewire::actingAs($this->user)
            ->test(ProgramaIndex::class)
            ->call('deletePrograma', $programa->id)
            ->assertDispatched('notify');

        $this->assertSoftDeleted('programas_formacion', ['id' => $programa->id]);
    }
}
