<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function invitado_no_puede_ver_aviso_verificacion(): void
    {
        $response = $this->get(route('verification.notice'));

        $response->assertRedirect(route('verificarLogin'));
        $this->assertGuest();
    }

    #[Test]
    public function usuario_no_verificado_puede_ver_aviso(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertStatus(200);
        $response->assertViewIs('user.verify-email');
    }

    #[Test]
    public function usuario_verificado_es_redirigido_desde_aviso(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertRedirect(route('home'));
    }

    #[Test]
    public function invitado_no_puede_reenviar_verificacion(): void
    {
        $response = $this->post(route('verification.resend'));

        $response->assertRedirect(route('verificarLogin'));
        $this->assertGuest();
    }
}
