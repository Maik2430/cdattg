<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AsistenceQrControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo('TOMAR ASISTENCIA');
    }

    #[Test]
    public function requiere_autenticacion(): void
    {
        $response = $this->get(route('asistence.web'));

        $response->assertRedirect(route('verificarLogin'));
    }

    #[Test]
    public function deniega_acceso_sin_permiso(): void
    {
        $userSinPermiso = User::factory()->create();

        $response = $this->actingAs($userSinPermiso)->get(route('asistence.web'));

        $response->assertStatus(403);
    }

    #[Test]
    public function puede_ver_selector_de_caracterizacion(): void
    {
        $response = $this->actingAs($this->user)->get(route('asistence.web'));

        $response->assertStatus(200);
        $response->assertViewIs('qr_asistence.caracter_selecter');
        $response->assertViewHasAll(['instructorFicha', 'diasFormacion']);
    }

    #[Test]
    public function exit_formation_redirige_si_no_hay_asistencias(): void
    {
        $response = $this->actingAs($this->user)
            ->from(route('asistence.web'))
            ->get(route('asistence.exitFormation', [
                'caracterizacion_id' => 999999,
            ]));

        $response->assertRedirect(route('asistence.web'));
        $response->assertSessionHas('error');
    }
}
