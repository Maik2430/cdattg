<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;

use App\Livewire\Asistencia\FinalizarAsistenciaModal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class FinalizarAsistenciaModalTest extends TestCase
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
    public function puede_montar_finalizar_asistencia_modal(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(FinalizarAsistenciaModal::class)
            ->assertStatus(200);
    }
}
