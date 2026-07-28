<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConfirmPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function invitado_no_puede_ver_formulario_confirmacion(): void
    {
        $response = $this->get(route('auth.password.confirm'));

        $response->assertRedirect(route('verificarLogin'));
        $this->assertGuest();
    }

    #[Test]
    public function usuario_autenticado_puede_ver_formulario_confirmacion(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('auth.password.confirm'));

        $response->assertStatus(200);
        $response->assertViewIs('vendor.adminlte.auth.passwords.confirm');
    }

    #[Test]
    public function invitado_no_puede_enviar_confirmacion(): void
    {
        $response = $this->post(route('auth.password.confirm.store'), [
            'password' => 'password',
        ]);

        $response->assertRedirect(route('verificarLogin'));
        $this->assertGuest();
    }

    #[Test]
    public function confirma_password_correcta_y_redirige(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->post(route('auth.password.confirm.store'), [
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertNotNull(session('auth.password_confirmed_at'));
    }
}
