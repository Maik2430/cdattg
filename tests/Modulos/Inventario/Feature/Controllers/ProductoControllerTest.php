<?php

namespace Tests\Feature\Inventario;

use App\Models\Inventario\Producto;
use App\Models\ParametroTema;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductoControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    // Constantes para permisos
    private const PERMISSION_VER_PRODUCTO = 'VER PRODUCTO';
    private const PERMISSION_VER_PRODUCTOS = 'VER PRODUCTOS';
    private const PERMISSION_CREAR_PRODUCTO = 'CREAR PRODUCTO';
    private const PERMISSION_EDITAR_PRODUCTO = 'EDITAR PRODUCTO';
    private const PERMISSION_ELIMINAR_PRODUCTO = 'ELIMINAR PRODUCTO';
    private const PERMISSION_BUSCAR_PRODUCTO = 'BUSCAR PRODUCTO';
    private const PERMISSION_VER_CATALOGO_PRODUCTO = 'VER CATALOGO PRODUCTO';

    // Constantes para rutas
    private const ROUTE_INDEX = 'inventario.productos.index';
    private const ROUTE_BUSCAR = 'inventario.productos.buscar';
    private const ROUTE_CREATE = 'inventario.productos.create';
    private const ROUTE_STORE = 'inventario.productos.store';
    private const ROUTE_SHOW = 'inventario.productos.show';
    private const ROUTE_EDIT = 'inventario.productos.edit';
    private const ROUTE_UPDATE = 'inventario.productos.update';
    private const ROUTE_DESTROY = 'inventario.productos.destroy';

    // Constantes para vistas
    private const VIEW_INDEX = 'inventario.productos.index';
    private const VIEW_CREATE = 'inventario.productos.create';

    // Constantes para datos
    private const PRODUCTO_ACTUALIZADO = 'Producto Actualizado';
    private const ROUTE_LOGIN = 'verificarLogin';

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ejecutarSeedersNecesarios();
        $this->crearPermisos();
        $this->crearUsuarioConPermisos();
    }

    private function ejecutarSeedersNecesarios(): void
    {
        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\ParametroSeeder::class,
            \Database\Seeders\TemaSeeder::class,
            \Database\Seeders\PaisSeeder::class,
            \Database\Seeders\DepartamentoSeeder::class,
            \Database\Seeders\MunicipioSeeder::class,
            \Database\Seeders\PersonaSeeder::class,
            \Database\Seeders\UsersSeeder::class,
            \Database\Seeders\RegionalSeeder::class,
            \Database\Seeders\SedeSeeder::class,
            \Database\Seeders\BloqueSeeder::class,
            \Database\Seeders\PisoSeeder::class,
            \Database\Seeders\AmbienteSeeder::class,
        ]);
    }

    private function crearPermisos(): void
    {
        Permission::firstOrCreate(['name' => self::PERMISSION_VER_PRODUCTO]);
        Permission::firstOrCreate(['name' => self::PERMISSION_VER_PRODUCTOS]);
        Permission::firstOrCreate(['name' => self::PERMISSION_CREAR_PRODUCTO]);
        Permission::firstOrCreate(['name' => self::PERMISSION_EDITAR_PRODUCTO]);
        Permission::firstOrCreate(['name' => self::PERMISSION_ELIMINAR_PRODUCTO]);
        Permission::firstOrCreate(['name' => self::PERMISSION_BUSCAR_PRODUCTO]);
        Permission::firstOrCreate(['name' => self::PERMISSION_VER_CATALOGO_PRODUCTO]);
    }

    private function crearUsuarioConPermisos(): void
    {
        $this->user = User::factory()->create();
        $this->user->givePermissionTo([
            self::PERMISSION_VER_PRODUCTO,
            self::PERMISSION_VER_PRODUCTOS,
        ]);
    }

    #[Test]
    public function puede_ver_listado_de_productos(): void
    {
        $this->actingAs($this->user);

        Producto::factory()->count(5)->create();

        $response = $this->get(route(self::ROUTE_INDEX));

        $response->assertStatus(200);
        $response->assertViewIs(self::VIEW_INDEX);
        $response->assertViewHas('productos');
    }

    #[Test]
    public function puede_buscar_productos(): void
    {
        $this->user->givePermissionTo(self::PERMISSION_BUSCAR_PRODUCTO);
        $this->actingAs($this->user);

        Producto::factory()->create(['name' => 'Producto Test']);

        $response = $this->get(route(self::ROUTE_BUSCAR, ['search' => 'Test']));

        $response->assertStatus(200);
    }

    #[Test]
    public function puede_ver_formulario_de_creacion(): void
    {
        $this->user->givePermissionTo(self::PERMISSION_CREAR_PRODUCTO);
        $this->actingAs($this->user);

        $response = $this->get(route(self::ROUTE_CREATE));

        $response->assertStatus(200);
        $response->assertViewIs(self::VIEW_CREATE);
        $response->assertViewHas('tiposProductos');
        $response->assertViewHas('unidadesMedida');
        $response->assertViewHas('estados');
        $response->assertViewHas('categorias');
        $response->assertViewHas('marcas');
    }

    private function asegurarParametroTemaEnTema(string $temaName, string $parametroDefault): ParametroTema
    {
        $existente = ParametroTema::whereHas('tema', function ($q) use ($temaName) {
            $q->where('name', $temaName);
        })->first();

        if ($existente) {
            return $existente;
        }

        $tema = \App\Models\Tema::firstOrCreate(
            ['name' => $temaName],
            [
                'status' => true,
                'user_create_id' => null,
                'user_edit_id' => null,
            ]
        );

        $parametro = \App\Models\Parametro::firstOrCreate(
            ['name' => $parametroDefault],
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

    private function asegurarParametroEnTema(string $temaName, string $parametroDefault): \App\Models\Parametro
    {
        $parametro = \App\Models\Parametro::whereHas('temas', function ($q) use ($temaName) {
            $q->where('name', $temaName);
        })->first();

        if ($parametro) {
            return $parametro;
        }

        $tema = \App\Models\Tema::firstOrCreate(
            ['name' => $temaName],
            [
                'status' => true,
                'user_create_id' => null,
                'user_edit_id' => null,
            ]
        );

        $parametro = \App\Models\Parametro::factory()->create([
            'name' => $parametroDefault,
            'status' => true,
            'user_create_id' => null,
            'user_edit_id' => null,
        ]);

        ParametroTema::firstOrCreate(
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

        return $parametro;
    }

    #[Test]
    public function puede_crear_producto(): void
    {
        $this->user->givePermissionTo(self::PERMISSION_CREAR_PRODUCTO);
        $this->actingAs($this->user);

        $tipoProducto = $this->asegurarParametroTemaEnTema(
            config('inventario.temas.tipos_producto'),
            'CONSUMIBLE'
        );
        $unidadMedida = $this->asegurarParametroTemaEnTema(
            config('inventario.temas.unidades_medida'),
            'UNIDADES'
        );
        $estado = $this->asegurarParametroTemaEnTema(
            config('inventario.temas.estados_producto'),
            'DISPONIBLE'
        );

        // Seeder usa CATEGORÍAS (con tilde); config usa CATEGORIAS
        $categoria = $this->asegurarParametroEnTema('CATEGORÍAS', 'CATEGORIA TEST');
        $marca = $this->asegurarParametroEnTema(config('inventario.temas.marcas'), 'MARCA TEST');

        $contratoConvenio = \App\Models\Inventario\ContratoConvenio::factory()->create();

        $ambiente = \App\Models\Ambiente::inRandomOrder()->first();
        if (! $ambiente) {
            $sede = \App\Models\Sede::inRandomOrder()->first()
                ?? \App\Models\Sede::factory()->create();
            $bloque = \App\Models\Bloque::factory()->create(['sede_id' => $sede->id]);
            $piso = \App\Models\Piso::factory()->create(['bloque_id' => $bloque->id]);
            $ambiente = \App\Models\Ambiente::factory()->create(['piso_id' => $piso->id]);
        }

        $proveedor = \App\Models\Inventario\Proveedor::factory()->create();

        $response = $this->post(route(self::ROUTE_STORE), [
            'name' => 'Producto de Prueba '.$this->faker->word(),
            'tipo_producto_id' => $tipoProducto->id,
            'descripcion' => $this->faker->sentence(),
            'peso' => $this->faker->randomFloat(2, 0.1, 100),
            'unidad_medida_id' => $unidadMedida->id,
            'cantidad' => $this->faker->numberBetween(1, 100),
            'estado_producto_id' => $estado->id,
            'categoria_id' => $categoria->id,
            'marca_id' => $marca->id,
            'contrato_convenio_id' => $contratoConvenio->id,
            'ambiente_id' => $ambiente->id,
            'proveedor_id' => $proveedor->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    public function no_puede_crear_producto_sin_permiso(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route(self::ROUTE_STORE), []);

        $response->assertStatus(403);
    }

    #[Test]
    public function puede_ver_detalles_de_producto(): void
    {
        $this->actingAs($this->user);

        $producto = Producto::factory()->create();

        $response = $this->get(route(self::ROUTE_SHOW, $producto->id));

        $response->assertStatus(200);
        $response->assertViewHas('producto');
    }

    #[Test]
    public function puede_ver_formulario_de_edicion(): void
    {
        $this->user->givePermissionTo(self::PERMISSION_EDITAR_PRODUCTO);
        $this->actingAs($this->user);

        $producto = Producto::factory()->create();

        $response = $this->get(route(self::ROUTE_EDIT, $producto->id));

        $response->assertStatus(200);
        $response->assertViewHas('producto');
        $response->assertViewHas('tiposProductos');
        $response->assertViewHas('unidadesMedida');
        $response->assertViewHas('estados');
        $response->assertViewHas('categorias');
        $response->assertViewHas('marcas');
    }

    #[Test]
    public function puede_actualizar_producto(): void
    {
        $this->user->givePermissionTo(self::PERMISSION_EDITAR_PRODUCTO);
        $this->actingAs($this->user);

        $producto = Producto::factory()->create();

        $response = $this->put(route(self::ROUTE_UPDATE, $producto->id), [
            'name' => self::PRODUCTO_ACTUALIZADO,
            'tipo_producto_id' => $producto->tipo_producto_id,
            'descripcion' => $producto->descripcion,
            'peso' => $producto->peso,
            'unidad_medida_id' => $producto->unidad_medida_id,
            'cantidad' => $producto->cantidad,
            'estado_producto_id' => $producto->estado_producto_id,
            'categoria_id' => $producto->categoria_id,
            'marca_id' => $producto->marca_id,
            'contrato_convenio_id' => $producto->contrato_convenio_id,
            'ambiente_id' => $producto->ambiente_id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'name' => strtoupper(self::PRODUCTO_ACTUALIZADO),
        ]);
    }

    #[Test]
    public function puede_eliminar_producto(): void
    {
        $this->user->givePermissionTo(self::PERMISSION_ELIMINAR_PRODUCTO);
        $this->actingAs($this->user);

        $producto = Producto::factory()->create();

        $response = $this->delete(route(self::ROUTE_DESTROY, $producto->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('productos', [
            'id' => $producto->id,
        ]);
    }

    #[Test]
    public function requiere_autenticacion_para_ver_productos(): void
    {
        $response = $this->get(route(self::ROUTE_INDEX));

        $response->assertRedirect(route(self::ROUTE_LOGIN));
    }
}
