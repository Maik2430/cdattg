<?php

namespace Tests\Feature\Livewire\Fichas;

use App\Livewire\Fichas\FichaIndex;
use App\Models\FichaCaracterizacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FichaIndexTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\ParametroSeeder::class,
            \Database\Seeders\PaisSeeder::class,
            \Database\Seeders\DepartamentoSeeder::class,
            \Database\Seeders\MunicipioSeeder::class,
        ]);

        $this->user = User::factory()->create();
        $this->user->assignRole('SUPER ADMINISTRADOR');
    }

    #[Test]
    public function puede_montar_el_componente(): void
    {
        Livewire::actingAs($this->user)
            ->test(FichaIndex::class)
            ->assertStatus(200);
    }

    #[Test]
    public function puede_cambiar_estado_de_ficha(): void
    {
        $ficha = FichaCaracterizacion::factory()->create(['status' => true]);

        Livewire::actingAs($this->user)
            ->test(FichaIndex::class)
            ->call('toggleStatus', $ficha->id)
            ->assertDispatched('notify');

        $this->assertFalse((bool) $ficha->fresh()->status);
    }

    #[Test]
    public function puede_eliminar_ficha_sin_aprendices(): void
    {
        $ficha = FichaCaracterizacion::factory()->create();

        Livewire::actingAs($this->user)
            ->test(FichaIndex::class)
            ->call('deleteFicha', $ficha->id)
            ->assertDispatched('notify');

        $this->assertDatabaseMissing('fichas_caracterizacion', ['id' => $ficha->id]);
    }
}
