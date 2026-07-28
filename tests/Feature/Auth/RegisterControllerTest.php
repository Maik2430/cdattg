<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
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
    public function puede_ver_formulario_registro(): void
    {
        $response = $this->get(route('registro'));

        $response->assertStatus(200);
        $response->assertViewIs('user.registro');
    }

    #[Test]
    public function formulario_registro_es_accesible_como_invitado(): void
    {
        $response = $this->get(route('registro'));

        $response->assertStatus(200);
        $this->assertGuest();
    }

    #[Test]
    public function usuario_autenticado_no_puede_ver_formulario_registro(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('registro'));

        $response->assertRedirect();
    }
}
