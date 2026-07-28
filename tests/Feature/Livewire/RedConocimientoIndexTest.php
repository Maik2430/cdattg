<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RedConocimiento\RedConocimientoIndex;
use App\Models\RedConocimiento;
use App\Models\Regional;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RedConocimientoIndexTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\PaisSeeder::class,
            \Database\Seeders\DepartamentoSeeder::class,
            \Database\Seeders\MunicipioSeeder::class,
        ]);

        $this->user = User::factory()->create();
        $this->user->assignRole('SUPER ADMINISTRADOR');

        if (! Regional::query()->exists()) {
            Regional::factory()->create([
                'user_create_id' => $this->user->id,
                'user_edit_id' => $this->user->id,
            ]);
        }
    }

    private function crearRed(array $overrides = []): RedConocimiento
    {
        $regional = Regional::query()->first() ?? Regional::factory()->create();

        return RedConocimiento::factory()->create(array_merge([
            'regionals_id' => $regional->id,
            'nombre' => 'Red Livewire '.uniqid(),
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ], $overrides));
    }

    #[Test]
    public function puede_montar_red_conocimiento_index(): void
    {
        Livewire::actingAs($this->user)
            ->test(RedConocimientoIndex::class)
            ->assertStatus(200);
    }

    #[Test]
    public function puede_cambiar_estado_de_red(): void
    {
        $red = $this->crearRed(['status' => true]);

        Livewire::actingAs($this->user)
            ->test(RedConocimientoIndex::class)
            ->call('toggleStatus', $red->id)
            ->assertDispatched('notify');

        $this->assertFalse((bool) $red->fresh()->status);
    }

    #[Test]
    public function puede_eliminar_red_sin_programas(): void
    {
        $red = $this->crearRed();

        Livewire::actingAs($this->user)
            ->test(RedConocimientoIndex::class)
            ->call('deleteRed', $red->id)
            ->assertDispatched('notify');

        $this->assertDatabaseMissing('red_conocimientos', ['id' => $red->id]);
    }
}
