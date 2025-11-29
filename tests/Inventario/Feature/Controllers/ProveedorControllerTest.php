<?php

namespace Tests\Feature\Inventario;

use Tests\TestCase;
use App\Models\User;
use App\Models\Inventario\Proveedor;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Pais;
use App\Models\ParametroTema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;

class ProveedorControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Departamento $departamento;
    protected Municipio $municipio;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ejecutar migraciones y seeders de todos los módulos
        $this->migrateDatabases();
        
        // Asegurar que los seeders se ejecuten después de RefreshDatabase
        if (!\App\Models\Pais::where('pais', 'COLOMBIA')->exists()) {
            $this->artisan('db:seed', ['--force' => true]);
        }

        // Crear país si no existe
        $pais = Pais::firstOrCreate(
            ['pais' => 'COLOMBIA'],
            ['status' => true]
        );

        // Crear departamento y municipio para los tests
        $this->departamento = Departamento::firstOrCreate(
            ['departamento' => 'ANTIOQUIA'],
            [
                'pais_id' => $pais->id,
                'status' => true,
            ]
        );

        $this->municipio = Municipio::firstOrCreate(
            [
                'municipio' => 'MEDELLIN',
                'departamento_id' => $this->departamento->id,
            ],
            ['status' => true]
        );

        // Crear estado para proveedores (necesario para el factory)
        $temaEstados = \App\Models\Tema::firstOrCreate(
            ['name' => 'ESTADOS'],
            [
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ]
        );

        $estado = \App\Models\Parametro::firstOrCreate(
            ['name' => 'ACTIVO'],
            [
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ]
        );

        ParametroTema::firstOrCreate(
            [
                'parametro_id' => $estado->id,
                'tema_id' => $temaEstados->id,
            ],
            [
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ]
        );

        // Crear permisos necesarios
        Permission::firstOrCreate(['name' => 'VER PROVEEDOR']);
        Permission::firstOrCreate(['name' => 'CREAR PROVEEDOR']);
        Permission::firstOrCreate(['name' => 'EDITAR PROVEEDOR']);
        Permission::firstOrCreate(['name' => 'ELIMINAR PROVEEDOR']);

        // Crear usuario con permisos
        $this->user = User::factory()->create();
        $this->user->givePermissionTo('VER PROVEEDOR');
    }

    #[Test]
    public function puede_ver_listado_de_proveedores()
    {
        $this->actingAs($this->user);

        // Crear algunos proveedores
        Proveedor::factory()->count(3)->create([
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
        ]);

        $response = $this->get(route('inventario.proveedores.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventario.proveedores.index');
        $response->assertViewHas('proveedores');
    }

    #[Test]
    public function puede_buscar_proveedores_por_nombre()
    {
        $this->actingAs($this->user);

        $proveedor = Proveedor::factory()->create([
            'proveedor' => 'TECNOLOGIA SISTEMAS LTDA',
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
        ]);

        $response = $this->get(route('inventario.proveedores.index', ['search' => 'TECNOLOGIA']));

        $response->assertStatus(200);
        $response->assertSee('TECNOLOGIA', false);
    }

    #[Test]
    public function puede_ver_formulario_de_creacion()
    {
        $this->user->givePermissionTo('CREAR PROVEEDOR');
        $this->actingAs($this->user);

        $response = $this->get(route('inventario.proveedores.create'));

        $response->assertStatus(200);
        $response->assertViewIs('inventario.proveedores.create');
        $response->assertViewHas('departamentos');
        $response->assertViewHas('municipios');
    }

    #[Test]
    public function puede_crear_proveedor()
    {
        $this->user->givePermissionTo('CREAR PROVEEDOR');
        $this->actingAs($this->user);

        $estadoId = ParametroTema::whereHas('tema', function($q) {
            $q->where('name', 'ESTADOS');
        })->first()->id ?? 1;

        $response = $this->post(route('inventario.proveedores.store'), [
            'proveedor' => 'NUEVO PROVEEDOR LTDA',
            'nit' => '900123456-7',
            'email' => 'contacto@proveedor.com',
            'telefono' => '6012345678',
            'direccion' => 'Calle 123 #45-67',
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
            'contacto' => 'JUAN PEREZ',
            'estado_id' => $estadoId,
        ]);

        $response->assertRedirect(route('inventario.proveedores.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('proveedores', [
            'proveedor' => 'NUEVO PROVEEDOR LTDA',
            'nit' => '900123456-7',
        ]);
    }

    #[Test]
    public function no_puede_crear_proveedor_sin_permiso()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('inventario.proveedores.store'), [
            'proveedor' => 'PROVEEDOR SIN PERMISO',
            'nit' => '900123456-7',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function puede_ver_detalles_de_proveedor()
    {
        $this->actingAs($this->user);

        $proveedor = Proveedor::factory()->create([
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
        ]);

        $response = $this->get(route('inventario.proveedores.show', $proveedor->id));

        $response->assertStatus(200);
        $response->assertViewIs('inventario.proveedores.show');
        $response->assertViewHas('proveedor');
    }

    #[Test]
    public function puede_ver_formulario_de_edicion()
    {
        $this->user->givePermissionTo('EDITAR PROVEEDOR');
        $this->actingAs($this->user);

        $proveedor = Proveedor::factory()->create([
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
        ]);

        $response = $this->get(route('inventario.proveedores.edit', $proveedor->id));

        $response->assertStatus(200);
        $response->assertViewIs('inventario.proveedores.edit');
        $response->assertViewHas('proveedor');
        $response->assertViewHas('departamentos');
        $response->assertViewHas('municipios');
    }

    #[Test]
    public function puede_actualizar_proveedor()
    {
        $this->user->givePermissionTo('EDITAR PROVEEDOR');
        $this->actingAs($this->user);

        $proveedor = Proveedor::factory()->create([
            'proveedor' => 'PROVEEDOR ORIGINAL',
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
        ]);

        $estadoId = ParametroTema::whereHas('tema', function($q) {
            $q->where('name', 'ESTADOS');
        })->first()->id ?? 1;

        $response = $this->put(route('inventario.proveedores.update', $proveedor->id), [
            'proveedor' => 'PROVEEDOR ACTUALIZADO',
            'nit' => $proveedor->nit,
            'email' => 'nuevo@email.com',
            'telefono' => $proveedor->telefono,
            'direccion' => $proveedor->direccion,
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
            'contacto' => $proveedor->contacto,
            'estado_id' => $estadoId,
        ]);

        $response->assertRedirect(route('inventario.proveedores.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('proveedores', [
            'id' => $proveedor->id,
            'proveedor' => 'PROVEEDOR ACTUALIZADO',
        ]);
    }

    #[Test]
    public function no_puede_actualizar_proveedor_sin_permiso()
    {
        $this->actingAs($this->user);

        $proveedor = Proveedor::factory()->create([
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
        ]);

        $response = $this->put(route('inventario.proveedores.update', $proveedor->id), [
            'proveedor' => 'PROVEEDOR ACTUALIZADO',
            'nit' => $proveedor->nit,
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function puede_eliminar_proveedor()
    {
        $this->user->givePermissionTo('ELIMINAR PROVEEDOR');
        $this->actingAs($this->user);

        $proveedor = Proveedor::factory()->create([
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
        ]);

        $response = $this->delete(route('inventario.proveedores.destroy', $proveedor->id));

        $response->assertRedirect(route('inventario.proveedores.index'));
        $response->assertSessionHas('success');
    }

    #[Test]
    public function no_puede_eliminar_proveedor_sin_permiso()
    {
        $this->actingAs($this->user);

        $proveedor = Proveedor::factory()->create([
            'departamento_id' => $this->departamento->id,
            'municipio_id' => $this->municipio->id,
        ]);

        $response = $this->delete(route('inventario.proveedores.destroy', $proveedor->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function puede_obtener_municipios_por_departamento()
    {
        $this->actingAs($this->user);

        // Crear otro municipio en el mismo departamento
        $municipio2 = Municipio::firstOrCreate(
            [
                'municipio' => 'BOGOTA',
                'departamento_id' => $this->departamento->id,
            ],
            ['status' => true]
        );

        $response = $this->getJson(
            route('inventario.proveedores.municipios', $this->departamento->id)
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'municipio']
        ]);
        
        // Verificar que retorna los municipios del departamento
        $municipios = $response->json();
        $this->assertGreaterThanOrEqual(1, count($municipios));
    }

    #[Test]
    public function retorna_array_vacio_si_departamento_no_tiene_municipios()
    {
        $this->actingAs($this->user);

        // Crear un departamento sin municipios
        $departamentoVacio = Departamento::factory()->create();

        $response = $this->getJson(
            route('inventario.proveedores.municipios', $departamentoVacio->id)
        );

        $response->assertStatus(200);
        $response->assertJson([]);
    }
}

