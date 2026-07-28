<?php

namespace Tests\Feature\Inventario;

use App\Models\Inventario\Orden;
use App\Models\Inventario\Producto;
use App\Models\ParametroTema;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class OrdenControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    // Constantes para permisos
    private const PERMISSION_VER_ORDEN = 'VER ORDEN';
    private const PERMISSION_CREAR_ORDEN = 'CREAR ORDEN';
    private const PERMISSION_EDITAR_ORDEN = 'EDITAR ORDEN';
    private const PERMISSION_ELIMINAR_ORDEN = 'ELIMINAR ORDEN';
    private const PERMISSION_APROBAR_ORDEN = 'APROBAR ORDEN';
    private const PERMISSION_COMPLETAR_ORDEN = 'COMPLETAR ORDEN';

    // Constantes para rutas
    private const ROUTE_INDEX = 'inventario.ordenes.index';
    private const ROUTE_STORE = 'inventario.prestamos-salidas.store';
    private const ROUTE_SHOW = 'inventario.ordenes.show';
    private const ROUTE_DESTROY = 'inventario.ordenes.destroy';

    // Constantes para vistas
    private const VIEW_INDEX = 'inventario.ordenes.index';

    // Constantes para datos
    private const ROUTE_LOGIN = 'verificarLogin';

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Desactivar CSRF para tests
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ]);

        // Seeders base para datos realistas
        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\ParametroSeeder::class,
            \Database\Seeders\TemaSeeder::class,
        ]);

        // Crear permisos necesarios
        Permission::firstOrCreate(['name' => self::PERMISSION_VER_ORDEN]);
        Permission::firstOrCreate(['name' => self::PERMISSION_CREAR_ORDEN]);
        Permission::firstOrCreate(['name' => self::PERMISSION_EDITAR_ORDEN]);
        Permission::firstOrCreate(['name' => self::PERMISSION_ELIMINAR_ORDEN]);
        Permission::firstOrCreate(['name' => self::PERMISSION_APROBAR_ORDEN]);
        Permission::firstOrCreate(['name' => self::PERMISSION_COMPLETAR_ORDEN]);

        // Crear usuario con permisos usando factory
        $this->user = User::factory()->create();
        $this->user->givePermissionTo(self::PERMISSION_VER_ORDEN);
    }

    #[Test]
    public function puede_ver_listado_de_ordenes(): void
    {
        $this->actingAs($this->user);

        Orden::factory()->count(5)->create();

        $response = $this->get(route(self::ROUTE_INDEX));

        $response->assertStatus(200);
        $response->assertViewIs(self::VIEW_INDEX);
        $response->assertViewHas('ordenes');
    }

    #[Test]
    public function puede_buscar_ordenes(): void
    {
        $this->actingAs($this->user);

        Orden::factory()->create(['descripcion_orden' => 'Orden Test']);

        $response = $this->get(route(self::ROUTE_INDEX, ['search' => 'Test']));

        $response->assertStatus(200);
        $response->assertViewHas('ordenes');
    }

    private function asegurarTema(string $name): \App\Models\Tema
    {
        return \App\Models\Tema::firstOrCreate(
            ['name' => $name],
            [
                'status' => true,
                'user_create_id' => null,
                'user_edit_id' => null,
            ]
        );
    }

    private function asegurarParametroTema(string $temaName, string $parametroName): ParametroTema
    {
        $existente = ParametroTema::whereHas('tema', function ($q) use ($temaName) {
            $q->where('name', $temaName);
        })->whereHas('parametro', function ($q) use ($parametroName) {
            $q->where('name', $parametroName);
        })->first();

        if ($existente) {
            return $existente;
        }

        $tema = $this->asegurarTema($temaName);

        $parametro = \App\Models\Parametro::firstOrCreate(
            ['name' => $parametroName],
            [
                'status' => true,
                'user_create_id' => null,
                'user_edit_id' => null,
            ]
        );

        return ParametroTema::firstOrCreate(
            [
                'parametro_id' => $parametro->id,
                'tema_id' => $tema->id,
            ],
            [
                'status' => true,
                'user_create_id' => null,
                'user_edit_id' => null,
            ]
        );
    }

    #[Test]
    public function puede_crear_orden(): void
    {
        $this->user->givePermissionTo(self::PERMISSION_CREAR_ORDEN);
        $this->actingAs($this->user);

        $temaTiposOrden = config('inventario.temas.tipos_orden');
        $temaEstadosOrden = config('inventario.temas.estados_orden');

        $this->asegurarTema($temaTiposOrden);
        $this->asegurarTema($temaEstadosOrden);

        // Seeder usa PRÉSTAMO (con tilde); el controller acepta "prestamo"
        $this->asegurarParametroTema($temaTiposOrden, 'PRÉSTAMO');
        $this->asegurarParametroTema($temaTiposOrden, 'PRESTAMO');
        $this->asegurarParametroTema($temaEstadosOrden, 'EN ESPERA');

        $producto = Producto::factory()->create(['cantidad' => 10]);

        $carrito = json_encode([
            [
                'id' => $producto->id,
                'quantity' => 2,
            ],
        ]);

        $response = $this->post(route(self::ROUTE_STORE), [
            'rol' => 'Instructor',
            'programa_formacion' => 'Programa de Prueba',
            'tipo' => 'prestamo',
            'fecha_devolucion' => now()->addDays(7)->format('Y-m-d'),
            'descripcion' => 'Orden de Prueba',
            'carrito' => $carrito,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    #[Test]
    public function no_puede_crear_orden_sin_permiso(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route(self::ROUTE_STORE), []);

        $response->assertStatus(403);
    }

    #[Test]
    public function puede_ver_detalles_de_orden(): void
    {
        $this->actingAs($this->user);

        $orden = Orden::factory()->create();

        $response = $this->get(route(self::ROUTE_SHOW, $orden->id));

        $response->assertStatus(200);
        $response->assertViewHas('orden');
    }

    #[Test]
    public function puede_eliminar_orden(): void
    {
        $this->user->givePermissionTo(self::PERMISSION_ELIMINAR_ORDEN);
        $this->actingAs($this->user);

        $orden = Orden::factory()->create();

        $response = $this->delete(route(self::ROUTE_DESTROY, $orden->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('ordenes', [
            'id' => $orden->id,
        ]);
    }

    #[Test]
    public function requiere_autenticacion_para_ver_ordenes(): void
    {
        $response = $this->get(route(self::ROUTE_INDEX));

        $response->assertRedirect(route(self::ROUTE_LOGIN));
    }
}
