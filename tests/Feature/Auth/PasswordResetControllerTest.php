<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function puede_ver_formulario_solicitud_reset(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertViewIs('adminlte::auth.passwords.email');
        $this->assertGuest();
    }

    #[Test]
    public function puede_ver_formulario_reset_con_token(): void
    {
        $response = $this->get(route('password.reset', [
            'token' => 'token-de-prueba',
            'email' => 'user@example.com',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('vendor.adminlte.auth.passwords.reset');
        $response->assertViewHas('token', 'token-de-prueba');
        $response->assertViewHas('email', 'user@example.com');
    }

    #[Test]
    public function formulario_solicitud_es_accesible_como_invitado(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $this->assertGuest();
    }
}
