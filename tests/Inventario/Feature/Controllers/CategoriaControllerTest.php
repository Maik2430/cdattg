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

class CategoriaControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Tema $temaCategorias;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ejecutar migraciones y seeders de todos los módulos
        $this->migrateDatabases();
        
        // Asegurar que los seeders se ejecuten después de RefreshDatabase
        // RefreshDatabase se ejecuta automáticamente, pero necesitamos los seeders
        if (!\App\Models\Tema::where('name', 'CATEGORIAS')->exists()) {
            $this->artisan('db:seed', ['--force' => true]);
        }

        // Crear tema CATEGORIAS
        $this->temaCategorias = Tema::firstOrCreate(
            ['name' => 'CATEGORIAS'],
            [
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ]
        );

        // Crear permisos necesarios
        Permission::firstOrCreate(['name' => 'VER CATEGORIA']);
        Permission::firstOrCreate(['name' => 'CREAR CATEGORIA']);
        Permission::firstOrCreate(['name' => 'EDITAR CATEGORIA']);
        Permission::firstOrCreate(['name' => 'ELIMINAR CATEGORIA']);

        // Crear usuario con permisos
        $this->user = User::factory()->create();
        $this->user->givePermissionTo('VER CATEGORIA');
    }

    #[Test]
    public function puede_ver_listado_de_categorias()
    {
        $this->actingAs($this->user);

        // Crear algunas categorías
        $categoria1 = Parametro::factory()->create(['name' => 'CATEGORIA 1']);
        $categoria2 = Parametro::factory()->create(['name' => 'CATEGORIA 2']);
        
        ParametroTema::create([
            'parametro_id' => $categoria1->id,
            'tema_id' => $this->temaCategorias->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        ParametroTema::create([
            'parametro_id' => $categoria2->id,
            'tema_id' => $this->temaCategorias->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->get(route('inventario.categorias.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventario.categorias.index');
        $response->assertViewHas('categorias');
    }

    #[Test]
    public function puede_buscar_categorias_por_nombre()
    {
        $this->actingAs($this->user);

        $categoria = Parametro::factory()->create(['name' => 'ELECTRONICA']);
        
        ParametroTema::create([
            'parametro_id' => $categoria->id,
            'tema_id' => $this->temaCategorias->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->get(route('inventario.categorias.index', ['search' => 'ELECTRONICA']));

        $response->assertStatus(200);
        $response->assertSee('ELECTRONICA', false);
    }

    #[Test]
    public function puede_ver_formulario_de_creacion()
    {
        $this->user->givePermissionTo('CREAR CATEGORIA');
        $this->actingAs($this->user);

        $response = $this->get(route('inventario.categorias.create'));

        $response->assertStatus(200);
        $response->assertViewIs('inventario.categorias.create');
    }

    #[Test]
    public function puede_crear_categoria()
    {
        $this->user->givePermissionTo('CREAR CATEGORIA');
        $this->actingAs($this->user);

        $response = $this->post(route('inventario.categorias.store'), [
            'name' => 'NUEVA CATEGORIA',
        ]);

        $response->assertRedirect(route('inventario.categorias.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('parametros', [
            'name' => 'NUEVA CATEGORIA',
        ]);
    }

    #[Test]
    public function no_puede_crear_categoria_sin_permiso()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('inventario.categorias.store'), [
            'name' => 'CATEGORIA SIN PERMISO',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function puede_ver_detalles_de_categoria()
    {
        $this->actingAs($this->user);

        $categoria = Parametro::factory()->create(['name' => 'CATEGORIA TEST']);
        
        ParametroTema::create([
            'parametro_id' => $categoria->id,
            'tema_id' => $this->temaCategorias->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->get(route('inventario.categorias.show', $categoria->id));

        $response->assertStatus(200);
        $response->assertViewIs('inventario.categorias.show');
        $response->assertViewHas('categoria');
    }

    #[Test]
    public function puede_ver_formulario_de_edicion()
    {
        $this->user->givePermissionTo('EDITAR CATEGORIA');
        $this->actingAs($this->user);

        $categoria = Parametro::factory()->create(['name' => 'CATEGORIA EDITAR']);
        
        ParametroTema::create([
            'parametro_id' => $categoria->id,
            'tema_id' => $this->temaCategorias->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->get(route('inventario.categorias.edit', $categoria->id));

        $response->assertStatus(200);
        $response->assertViewIs('inventario.categorias.edit');
        $response->assertViewHas('categoria');
    }

    #[Test]
    public function puede_actualizar_categoria()
    {
        $this->user->givePermissionTo('EDITAR CATEGORIA');
        $this->actingAs($this->user);

        $categoria = Parametro::factory()->create(['name' => 'CATEGORIA ORIGINAL']);
        
        ParametroTema::create([
            'parametro_id' => $categoria->id,
            'tema_id' => $this->temaCategorias->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->put(route('inventario.categorias.update', $categoria->id), [
            'name' => 'CATEGORIA ACTUALIZADA',
        ]);

        $response->assertRedirect(route('inventario.categorias.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('parametros', [
            'id' => $categoria->id,
            'name' => 'CATEGORIA ACTUALIZADA',
        ]);
    }

    #[Test]
    public function no_puede_actualizar_categoria_sin_permiso()
    {
        $this->actingAs($this->user);

        $categoria = Parametro::factory()->create(['name' => 'CATEGORIA TEST']);
        
        ParametroTema::create([
            'parametro_id' => $categoria->id,
            'tema_id' => $this->temaCategorias->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->put(route('inventario.categorias.update', $categoria->id), [
            'name' => 'CATEGORIA ACTUALIZADA',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function puede_eliminar_categoria()
    {
        $this->user->givePermissionTo('ELIMINAR CATEGORIA');
        $this->actingAs($this->user);

        $categoria = Parametro::factory()->create(['name' => 'CATEGORIA ELIMINAR']);
        
        ParametroTema::create([
            'parametro_id' => $categoria->id,
            'tema_id' => $this->temaCategorias->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->delete(route('inventario.categorias.destroy', $categoria->id));

        $response->assertRedirect(route('inventario.categorias.index'));
        $response->assertSessionHas('success');
    }

    #[Test]
    public function no_puede_eliminar_categoria_sin_permiso()
    {
        $this->actingAs($this->user);

        $categoria = Parametro::factory()->create(['name' => 'CATEGORIA TEST']);
        
        ParametroTema::create([
            'parametro_id' => $categoria->id,
            'tema_id' => $this->temaCategorias->id,
            'status' => true,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $response = $this->delete(route('inventario.categorias.destroy', $categoria->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function retorna_error_si_no_existe_tema_categorias()
    {
        $this->actingAs($this->user);

        // Eliminar el tema CATEGORIAS
        $this->temaCategorias->delete();

        $response = $this->get(route('inventario.categorias.index'));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}

