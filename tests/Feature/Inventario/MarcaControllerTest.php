<?php

namespace Tests\Feature\Inventario;

use Tests\TestCase;
use App\Models\User;
use App\Models\Parametro;
use App\Models\Tema;
use App\Models\ParametroTema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;

class MarcaControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Tema $temaMarcas;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ejecutar migraciones y seeders de todos los módulos
        $this->migrateDatabases();
        
        // Asegurar que los seeders se ejecuten después de RefreshDatabase
        if (!\App\Models\Tema::where('name', 'MARCAS')->exists()) {
            $this->artisan('db:seed', ['--force' => true]);
        }

        // Crear tema MARCAS
        $this->temaMarcas = Tema::firstOrCreate(
            ['name' => 'MARCAS'],
            [
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ]
        );

        // Crear permisos necesarios
        Permission::firstOrCreate(['name' => 'VER MARCA']);
        Permission::firstOrCreate(['name' => 'CREAR MARCA']);
        Permission::firstOrCreate(['name' => 'EDITAR MARCA']);
        Permission::firstOrCreate(['name' => 'ELIMINAR MARCA']);

        // Crear usuario con permisos
        $this->user = User::factory()->create();
        $this->user->givePermissionTo('VER MARCA');
    }

    #[Test]
    public function puede_ver_listado_de_marcas()
    {
        $this->actingAs($this->user);

        // Crear algunas marcas
        $marca1 = Parametro::factory()->create(['name' => 'MARCA 1']);
        $marca2 = Parametro::factory()->create(['name' => 'MARCA 2']);
        
        ParametroTema::create([
            'parametro_id' => $marca1->id,
            'tema_id' => $this->temaMarcas->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        ParametroTema::create([
            'parametro_id' => $marca2->id,
            'tema_id' => $this->temaMarcas->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->get(route('inventario.marcas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventario.marcas.index');
        $response->assertViewHas('marcas');
    }

    #[Test]
    public function puede_buscar_marcas_por_nombre()
    {
        $this->actingAs($this->user);

        $marca = Parametro::factory()->create(['name' => 'SAMSUNG']);
        
        ParametroTema::create([
            'parametro_id' => $marca->id,
            'tema_id' => $this->temaMarcas->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->get(route('inventario.marcas.index', ['search' => 'SAMSUNG']));

        $response->assertStatus(200);
        $response->assertSee('SAMSUNG', false);
    }

    #[Test]
    public function puede_ver_formulario_de_creacion()
    {
        $this->user->givePermissionTo('CREAR MARCA');
        $this->actingAs($this->user);

        $response = $this->get(route('inventario.marcas.create'));

        $response->assertStatus(200);
        $response->assertViewIs('inventario.marcas.create');
    }

    #[Test]
    public function puede_crear_marca()
    {
        $this->user->givePermissionTo('CREAR MARCA');
        $this->actingAs($this->user);

        $response = $this->post(route('inventario.marcas.store'), [
            'name' => 'NUEVA MARCA',
        ]);

        $response->assertRedirect(route('inventario.marcas.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('parametros', [
            'name' => 'NUEVA MARCA',
        ]);
    }

    #[Test]
    public function no_puede_crear_marca_sin_permiso()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('inventario.marcas.store'), [
            'name' => 'MARCA SIN PERMISO',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function puede_ver_detalles_de_marca()
    {
        $this->actingAs($this->user);

        $marca = Parametro::factory()->create(['name' => 'MARCA TEST']);
        
        ParametroTema::create([
            'parametro_id' => $marca->id,
            'tema_id' => $this->temaMarcas->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->get(route('inventario.marcas.show', $marca->id));

        $response->assertStatus(200);
        $response->assertViewIs('inventario.marcas.show');
        $response->assertViewHas('marca');
    }

    #[Test]
    public function puede_ver_formulario_de_edicion()
    {
        $this->user->givePermissionTo('EDITAR MARCA');
        $this->actingAs($this->user);

        $marca = Parametro::factory()->create(['name' => 'MARCA EDITAR']);
        
        ParametroTema::create([
            'parametro_id' => $marca->id,
            'tema_id' => $this->temaMarcas->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->get(route('inventario.marcas.edit', $marca->id));

        $response->assertStatus(200);
        $response->assertViewIs('inventario.marcas.edit');
        $response->assertViewHas('marca');
    }

    #[Test]
    public function puede_actualizar_marca()
    {
        $this->user->givePermissionTo('EDITAR MARCA');
        $this->actingAs($this->user);

        $marca = Parametro::factory()->create(['name' => 'MARCA ORIGINAL']);
        
        ParametroTema::create([
            'parametro_id' => $marca->id,
            'tema_id' => $this->temaMarcas->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->put(route('inventario.marcas.update', $marca->id), [
            'name' => 'MARCA ACTUALIZADA',
        ]);

        $response->assertRedirect(route('inventario.marcas.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('parametros', [
            'id' => $marca->id,
            'name' => 'MARCA ACTUALIZADA',
        ]);
    }

    #[Test]
    public function no_puede_actualizar_marca_sin_permiso()
    {
        $this->actingAs($this->user);

        $marca = Parametro::factory()->create(['name' => 'MARCA TEST']);
        
        ParametroTema::create([
            'parametro_id' => $marca->id,
            'tema_id' => $this->temaMarcas->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->put(route('inventario.marcas.update', $marca->id), [
            'name' => 'MARCA ACTUALIZADA',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function puede_eliminar_marca()
    {
        $this->user->givePermissionTo('ELIMINAR MARCA');
        $this->actingAs($this->user);

        $marca = Parametro::factory()->create(['name' => 'MARCA ELIMINAR']);
        
        ParametroTema::create([
            'parametro_id' => $marca->id,
            'tema_id' => $this->temaMarcas->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->delete(route('inventario.marcas.destroy', $marca->id));

        $response->assertRedirect(route('inventario.marcas.index'));
        $response->assertSessionHas('success');
    }

    #[Test]
    public function no_puede_eliminar_marca_sin_permiso()
    {
        $this->actingAs($this->user);

        $marca = Parametro::factory()->create(['name' => 'MARCA TEST']);
        
        ParametroTema::create([
            'parametro_id' => $marca->id,
            'tema_id' => $this->temaMarcas->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->delete(route('inventario.marcas.destroy', $marca->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function retorna_error_si_no_existe_tema_marcas()
    {
        $this->actingAs($this->user);

        // Eliminar el tema MARCAS
        $this->temaMarcas->delete();

        $response = $this->get(route('inventario.marcas.index'));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}

