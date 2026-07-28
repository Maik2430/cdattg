<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AsistenciaAprendicesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);

        Permission::firstOrCreate(['name' => 'TOMAR ASISTENCIA']);
        Permission::firstOrCreate(['name' => 'VER PROGRAMA DE CARACTERIZACION']);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo('TOMAR ASISTENCIA');
    }

    #[Test]
    public function requiere_autenticacion_para_index(): void
    {
        $response = $this->get(route('asistencia.index'));

        $response->assertRedirect(route('verificarLogin'));
    }

    #[Test]
    public function usuario_autenticado_puede_consultar_listado_api(): void
    {
        // GET seguro del mismo controller (ruta web index choca con resource show).
        $response = $this->actingAs($this->user)
            ->getJson('/api/asistencia/getFicha/99999/JORNADA_INEXISTENTE');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Jornada no encontrada']);
    }

    #[Test]
    public function usuario_autenticado_con_permiso_puede_usar_endpoint_web(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('asistencia.getDocumentsByFicha'), []);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'ID de ficha no proporcionado']);
    }

    #[Test]
    public function usuario_sin_permiso_no_puede_acceder(): void
    {
        $userSinPermiso = User::factory()->create();

        $response = $this->actingAs($userSinPermiso)
            ->post(route('asistencia.getDocumentsByFicha'), []);

        $response->assertForbidden();
    }
}
