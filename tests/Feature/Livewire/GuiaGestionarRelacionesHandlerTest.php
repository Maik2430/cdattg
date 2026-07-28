<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;

use App\Livewire\GuiasAprendizaje\GestionarRelacionesHandler;
use App\Models\GuiasAprendizaje;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class GuiaGestionarRelacionesHandlerTest extends TestCase
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
        $guia = GuiasAprendizaje::factory()->create([
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(GestionarRelacionesHandler::class, ['guia' => $guia->id])
            ->assertStatus(200);
    }
}
