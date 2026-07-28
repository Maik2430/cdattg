<?php

namespace Tests\Feature\Complementarios;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CatalogoComplementarioControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([\Database\Seeders\RolePermissionSeeder::class]);

        $this->user = User::factory()->create();
        $this->user->assignRole('SUPER ADMINISTRADOR');
    }

    #[Test]
    public function requiere_autenticacion(): void
    {
        $response = $this->get(route('complementarios-ofertados.catalogo.import.create'));

        $response->assertRedirect();
    }

    #[Test]
    public function puede_ver_formulario_importar_catalogo(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('complementarios-ofertados.catalogo.import.create'));

        $response->assertStatus(200);
        $response->assertViewIs('complementarios.programas.admin.catalogo_import');
        $response->assertViewHas('maxFileSizeMb');
    }
}
