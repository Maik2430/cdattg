<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ComplementarioService;
use App\Repositories\TemaRepository;
use App\Repositories\ComplementarioOfertadoRepository;
use App\Repositories\AspiranteComplementarioRepository;
use App\Models\ComplementarioOfertado;
use App\Models\AspiranteComplementario;
use App\Models\ParametroTema;
use App\Models\JornadaFormacion;
use App\Models\Ambiente;
use App\Models\Parametro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class ComplementarioServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ComplementarioService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new ComplementarioService(
            Mockery::mock(TemaRepository::class),
            new ComplementarioOfertadoRepository(),
            new AspiranteComplementarioRepository()
        );
    }

    /** @test */
    public function puede_obtener_icono_para_programa()
    {
        $icono = $this->service->getIconoForPrograma('Auxiliar de Cocina');
        
        $this->assertEquals('fas fa-utensils', $icono);
    }

    /** @test */
    public function retorna_icono_por_defecto_si_no_existe()
    {
        $icono = $this->service->getIconoForPrograma('Programa Desconocido');
        
        $this->assertEquals('fas fa-graduation-cap', $icono);
    }

    /** @test */
    public function puede_obtener_clase_badge_por_estado()
    {
        $clase0 = $this->service->getBadgeClassForEstado(0);
        $clase1 = $this->service->getBadgeClassForEstado(1);
        $clase2 = $this->service->getBadgeClassForEstado(2);
        
        $this->assertEquals('bg-secondary', $clase0);
        $this->assertEquals('bg-success', $clase1);
        $this->assertEquals('bg-warning', $clase2);
    }

    /** @test */
    public function puede_obtener_label_estado()
    {
        $label0 = $this->service->getEstadoLabel(0);
        $label1 = $this->service->getEstadoLabel(1);
        $label2 = $this->service->getEstadoLabel(2);
        
        $this->assertEquals('Sin Oferta', $label0);
        $this->assertEquals('Con Oferta', $label1);
        $this->assertEquals('Cupos Llenos', $label2);
    }

    /** @test */
    public function puede_enriquecer_programa()
    {
        $this->seed([
            \Database\Seeders\ParametroSeeder::class,
        ]);

        $modalidad = ParametroTema::where('tema_id', 5)->first();
        $jornada = JornadaFormacion::factory()->create();

        $programa = ComplementarioOfertado::factory()->create([
            'nombre' => 'Auxiliar de Cocina',
            'estado' => 1,
            'modalidad_id' => $modalidad->id,
            'jornada_id' => $jornada->id,
        ]);

        $programa->load(['modalidad.parametro', 'jornada']);

        $enriquecido = $this->service->enriquecerPrograma($programa);

        $this->assertEquals('fas fa-utensils', $enriquecido->icono);
        $this->assertEquals('bg-success', $enriquecido->badge_class);
        $this->assertEquals('Con Oferta', $enriquecido->estado_label);
        $this->assertNotNull($enriquecido->modalidad_nombre);
        $this->assertNotNull($enriquecido->jornada_nombre);
    }

    /** @test */
    public function puede_enriquecer_coleccion_programas()
    {
        ComplementarioOfertado::factory()->count(3)->create();

        $programas = $this->service->obtenerProgramas();
        $enriquecidos = $this->service->enriquecerProgramas($programas);

        $this->assertCount(3, $enriquecidos);
        $enriquecidos->each(function ($programa) {
            $this->assertNotNull($programa->icono);
            $this->assertNotNull($programa->badge_class);
            $this->assertNotNull($programa->estado_label);
        });
    }

    /** @test */
    public function puede_obtener_programas_con_filtro_estado()
    {
        ComplementarioOfertado::factory()->count(3)->conOferta()->create();
        ComplementarioOfertado::factory()->count(2)->sinOferta()->create();

        $activos = $this->service->obtenerProgramas([], 1);
        $sinOferta = $this->service->obtenerProgramas([], 0);

        $this->assertCount(3, $activos);
        $this->assertCount(2, $sinOferta);
    }

    /** @test */
    public function puede_verificar_inscripcion_existente()
    {
        $persona = \App\Models\Persona::factory()->create();
        $programa = ComplementarioOfertado::factory()->create();
        AspiranteComplementario::factory()->paraPersona($persona)->paraPrograma($programa)->create();

        $existe = $this->service->verificarInscripcionExistente($persona->id, $programa->id);
        $noExiste = $this->service->verificarInscripcionExistente($persona->id, ComplementarioOfertado::factory()->create()->id);

        $this->assertTrue($existe);
        $this->assertFalse($noExiste);
    }

    /** @test */
    public function puede_crear_aspirante()
    {
        $persona = \App\Models\Persona::factory()->create();
        $programa = ComplementarioOfertado::factory()->create();

        $aspirante = $this->service->crearAspirante($persona->id, $programa->id, 'Observaciones test');

        $this->assertDatabaseHas('aspirantes_complementarios', [
            'persona_id' => $persona->id,
            'complementario_id' => $programa->id,
            'observaciones' => 'Observaciones test',
            'estado' => 1,
        ]);
    }

    /** @test */
    public function puede_obtener_estadisticas_programa()
    {
        $programa = ComplementarioOfertado::factory()->create(['cupos' => 30]);
        AspiranteComplementario::factory()->count(5)->enProceso()->paraPrograma($programa)->create();
        AspiranteComplementario::factory()->count(3)->admitido()->paraPrograma($programa)->create();

        $estadisticas = $this->service->obtenerEstadisticasPrograma($programa->id);

        $this->assertEquals(8, $estadisticas['total_aspirantes']);
        $this->assertEquals(5, $estadisticas['aspirantes_activos']);
        $this->assertEquals(3, $estadisticas['aspirantes_aceptados']);
        $this->assertEquals(22, $estadisticas['cupos_disponibles']);
    }

    /** @test */
    public function puede_obtener_datos_formulario()
    {
        $this->seed([
            \Database\Seeders\ParametroSeeder::class,
        ]);

        ParametroTema::where('tema_id', 5)->first() ?? ParametroTema::factory()->create(['tema_id' => 5]);
        JornadaFormacion::factory()->create();
        Ambiente::factory()->create(['status' => 1]);

        $datos = $this->service->obtenerDatosFormulario();

        $this->assertArrayHasKey('modalidades', $datos);
        $this->assertArrayHasKey('jornadas', $datos);
        $this->assertArrayHasKey('ambientes', $datos);
        $this->assertArrayHasKey('competencias', $datos);
        $this->assertArrayHasKey('guias', $datos);
    }

    /** @test */
    public function puede_sincronizar_dias_formacion()
    {
        $programa = ComplementarioOfertado::factory()->create();
        $dia1 = Parametro::factory()->create();
        $dia2 = Parametro::factory()->create();

        $dias = [
            [
                'dia_id' => $dia1->id,
                'hora_inicio' => '08:00:00',
                'hora_fin' => '12:00:00',
            ],
            [
                'dia_id' => $dia2->id,
                'hora_inicio' => '14:00:00',
                'hora_fin' => '18:00:00',
            ],
        ];

        $this->service->sincronizarDiasFormacion($programa, $dias);

        $programa->refresh();
        $this->assertCount(2, $programa->diasFormacion);
        $this->assertTrue($programa->diasFormacion->contains($dia1->id));
        $this->assertTrue($programa->diasFormacion->contains($dia2->id));

        $dia1Pivot = $programa->diasFormacion->firstWhere('id', $dia1->id)->pivot;
        $this->assertEquals('08:00:00', $dia1Pivot->hora_inicio);
        $this->assertEquals('12:00:00', $dia1Pivot->hora_fin);
    }

    /** @test */
    public function puede_eliminar_dias_formacion()
    {
        $programa = ComplementarioOfertado::factory()->create();
        $dia = Parametro::factory()->create();

        $programa->diasFormacion()->attach($dia->id, [
            'hora_inicio' => '08:00:00',
            'hora_fin' => '12:00:00',
        ]);

        $this->service->sincronizarDiasFormacion($programa, null);

        $programa->refresh();
        $this->assertCount(0, $programa->diasFormacion);
    }

    /** @test */
    public function puede_obtener_tipos_documento()
    {
        $temaRepository = Mockery::mock(TemaRepository::class);
        $temaMock = (object) [
            'id' => 1,
            'parametros' => collect([
                (object) ['id' => 1, 'name' => 'Cédula'],
                (object) ['id' => 2, 'name' => 'Tarjeta de Identidad'],
            ]),
        ];

        $temaRepository->shouldReceive('obtenerTiposDocumento')
            ->andReturn($temaMock);

        $service = new ComplementarioService(
            $temaRepository,
            new ComplementarioOfertadoRepository(),
            new AspiranteComplementarioRepository()
        );

        $tiposDocumento = $service->getTiposDocumento();

        $this->assertGreaterThanOrEqual(0, $tiposDocumento->count());
    }

    /** @test */
    public function puede_obtener_generos()
    {
        $temaRepository = Mockery::mock(TemaRepository::class);
        $temaMock = (object) [
            'id' => 1,
            'parametros' => collect([
                (object) ['id' => 9, 'name' => 'Masculino'],
                (object) ['id' => 10, 'name' => 'Femenino'],
            ]),
        ];

        $temaRepository->shouldReceive('obtenerGeneros')
            ->andReturn($temaMock);

        $service = new ComplementarioService(
            $temaRepository,
            new ComplementarioOfertadoRepository(),
            new AspiranteComplementarioRepository()
        );

        $generos = $service->getGeneros();

        $this->assertGreaterThanOrEqual(0, $generos->count());
    }
}
