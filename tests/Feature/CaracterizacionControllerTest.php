<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CaracterizacionControllerTest extends TestCase
{
    use RefreshDatabase;

    private const PERMISO_TOMAR_ASISTENCIA = 'TOMAR ASISTENCIA';

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);

        Permission::firstOrCreate(['name' => self::PERMISO_TOMAR_ASISTENCIA]);

        $this->user = User::factory()->create();
    }

    #[Test]
    public function requiere_autenticacion_para_ver_index(): void
    {
        $response = $this->get(route('caracterizacion.index'));

        $response->assertRedirect(route('verificarLogin'));
    }

    #[Test]
    public function requiere_autenticacion_para_ver_create(): void
    {
        $response = $this->get(route('caracterizacion.legacy.create'));

        $response->assertRedirect(route('verificarLogin'));
    }

    #[Test]
    public function no_puede_ver_caracter_index_sin_permiso(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('caracter.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function no_puede_ver_create_legacy_sin_permiso(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('caracterizacion.legacy.create'));

        $response->assertStatus(403);
    }

    #[Test]
    public function puede_ver_formulario_de_creacion_con_permiso(): void
    {
        $this->user->givePermissionTo(self::PERMISO_TOMAR_ASISTENCIA);
        $this->actingAs($this->user);

        $response = $this->get(route('caracterizacion.legacy.create'));

        $response->assertStatus(200);
        $response->assertViewIs('caracterizacion.create');
        $response->assertViewHas('fichas');
    }

    #[Test]
    public function puede_ver_caracter_index_con_permiso(): void
    {
        $this->user->givePermissionTo(self::PERMISO_TOMAR_ASISTENCIA);
        $this->actingAs($this->user);

        $response = $this->get(route('caracter.index'));

        $response->assertStatus(200);
        $response->assertViewIs('caracterizacion.index');
        $response->assertViewHas('caracteres');
    }
}
