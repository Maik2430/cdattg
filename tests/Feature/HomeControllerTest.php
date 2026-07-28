<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\ParametroSeeder::class,
            \Database\Seeders\TemaSeeder::class,
        ]);
    }

    #[Test]
    public function requiere_autenticacion(): void
    {
        $response = $this->get(route('home.index'));

        $response->assertRedirect(route('verificarLogin'));
    }

    #[Test]
    public function puede_ver_home_autenticado(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home.index'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertViewHasAll(['programas', 'programasInscritos', 'programasInscritosIds']);
    }
}
