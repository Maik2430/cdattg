<?php

namespace Tests\Feature\Complementarios;

use Tests\TestCase;
use App\Models\ComplementarioOfertado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProgramaComplementarioControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function puede_listar_programas_complementarios_admin()
    {
        $this->actingAs($this->user);
        ComplementarioOfertado::factory()->count(5)->create();

        $response = $this->get(route('complementarios-ofertados.index'));

        $response->assertStatus(200);
        $response->assertViewIs('complementarios.programas.admin.index');
        $response->assertViewHas('programas');
    }

    /** @test */
    public function puede_ver_formulario_creacion_programa()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('complementarios-ofertados.create'));

        $response->assertStatus(200);
        $response->assertViewIs('complementarios.programas.admin.create');
    }

    /** @test */
    public function puede_ver_programas_publicos()
    {
        ComplementarioOfertado::factory()->count(3)->conOferta()->create();
        ComplementarioOfertado::factory()->count(2)->sinOferta()->create();

        $response = $this->get(route('programas-complementarios.index'));

        $response->assertStatus(200);
        $response->assertViewIs('complementarios.programas.public.index');
        $response->assertViewHas('programas');
    }

    /** @test */
    public function puede_ver_programa_especifico_publico()
    {
        $programa = ComplementarioOfertado::factory()->conOferta()->create();

        $response = $this->get(route('programas-complementarios.show', $programa->id));

        $response->assertStatus(200);
        $response->assertViewIs('complementarios.programas.public.show');
    }

    /** @test */
    public function puede_obtener_datos_programa_para_edicion()
    {
        $this->actingAs($this->user);
        $programa = ComplementarioOfertado::factory()->create();

        $response = $this->get(route('complementarios-ofertados.edit', $programa->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'codigo',
            'nombre',
            'justificacion',
            'requisitos_ingreso',
            'duracion',
            'cupos',
            'estado',
            'modalidad_id',
            'jornada_id',
            'ambiente_id',
            'dias',
        ]);
    }

    /** @test */
    public function puede_crear_programa_complementario()
    {
        $this->actingAs($this->user);

        $data = [
            'codigo' => 'COMP0001',
            'nombre' => 'Programa Test',
            'justificacion' => 'Justificación de prueba',
            'requisitos_ingreso' => 'Requisitos de prueba',
            'duracion' => 60,
            'cupos' => 30,
            'estado' => 1,
            'modalidad_id' => 18,
            'jornada_id' => 1,
            'ambiente_id' => 1,
        ];

        $response = $this->post(route('complementarios-ofertados.store'), $data);

        $response->assertRedirect(route('complementarios-ofertados.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('complementarios_ofertados', [
            'codigo' => 'COMP0001',
            'nombre' => 'Programa Test',
        ]);
    }

    /** @test */
    public function puede_actualizar_programa_complementario()
    {
        $this->actingAs($this->user);
        $programa = ComplementarioOfertado::factory()->create();

        $data = [
            'codigo' => $programa->codigo,
            'nombre' => 'Programa Actualizado',
            'justificacion' => 'Nueva justificación',
            'requisitos_ingreso' => 'Nuevos requisitos',
            'duracion' => 80,
            'cupos' => 40,
            'estado' => 1,
            'modalidad_id' => $programa->modalidad_id,
            'jornada_id' => $programa->jornada_id,
            'ambiente_id' => $programa->ambiente_id,
        ];

        $response = $this->put(route('complementarios-ofertados.update', $programa->id), $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('complementarios_ofertados', [
            'id' => $programa->id,
            'nombre' => 'Programa Actualizado',
        ]);
    }

    /** @test */
    public function puede_eliminar_programa_complementario()
    {
        $this->actingAs($this->user);
        $programa = ComplementarioOfertado::factory()->create();

        $response = $this->delete(route('complementarios-ofertados.destroy', $programa->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('complementarios_ofertados', [
            'id' => $programa->id,
        ]);
    }
}
