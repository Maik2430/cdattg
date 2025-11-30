<?php

namespace Tests\Complementarios\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\ValidarSofiaJob;
use App\Services\Sofia\SofiaValidationService;
use App\Services\Sofia\SofiaValidationProcessor;
use App\Models\ComplementarioOfertado;
use App\Models\AspiranteComplementario;
use App\Models\Persona;
use App\Models\SofiaValidationProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Mockery;

class ValidarSofiaJobTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    private function createTestUser(): \App\Models\User
    {
        // Crear datos básicos de ubicación con IDs únicos para evitar conflictos
        static $userCount = 0;
        $userCount++;
        $id = $userCount;

        \App\Models\Pais::updateOrCreate(['id' => $id], ['pais' => 'Colombia'.$id, 'status' => 1]);
        \App\Models\Departamento::updateOrCreate(['id' => $id], ['pais_id' => $id, 'departamento' => 'Test Dept'.$id, 'status' => 1]);
        \App\Models\Municipio::updateOrCreate(['id' => $id], ['departamento_id' => $id, 'municipio' => 'Test City'.$id, 'status' => 1]);

        $persona = \App\Models\Persona::create([
            'tipo_documento' => 1,
            'numero_documento' => '123456'.$id,
            'primer_nombre' => 'Test'.$id,
            'segundo_nombre' => '',
            'primer_apellido' => 'User'.$id,
            'segundo_apellido' => '',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 1,
            'telefono' => '123456789'.$id,
            'celular' => '098765432'.$id,
            'email' => 'test'.$id.'@example.com',
            'pais_id' => $id,
            'departamento_id' => $id,
            'municipio_id' => $id,
            'direccion' => 'Dirección test'.$id,
            'status' => 1,
            'estado_sofia' => 1,
        ]);

        return \App\Models\User::create([
            'email' => 'test'.$id.'@example.com',
            'password' => bcrypt('password'),
            'status' => 1,
            'persona_id' => $persona->id,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Crear datos básicos necesarios para los tests
        \App\Models\Pais::updateOrCreate(['id' => 1], ['pais' => 'Colombia', 'status' => 1]);
        \App\Models\Departamento::updateOrCreate(['id' => 1], ['pais_id' => 1, 'departamento' => 'Test Dept', 'status' => 1]);
        \App\Models\Municipio::updateOrCreate(['id' => 1], ['departamento_id' => 1, 'municipio' => 'Test City', 'status' => 1]);

        // Crear modalidades y jornadas si existen las tablas
        if (\Illuminate\Support\Facades\Schema::hasTable('modalidades_formacion')) {
            \App\Models\ModalidadFormacion::updateOrCreate(['id' => 1], ['modalidad' => 'Presencial', 'status' => 1]);
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('jornadas_formacion')) {
            \App\Models\JornadaFormacion::updateOrCreate(['id' => 1], ['jornada' => 'Diurna', 'status' => 1]);
        }
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function puede_crear_job(): void
    {
        $job = new ValidarSofiaJob(1, 1, 1);

        $this->assertInstanceOf(ValidarSofiaJob::class, $job);
        $this->assertEquals(1, $job->getComplementarioId());
        $this->assertEquals(1, $job->getUserId());
        $this->assertEquals(1, $job->getProgressId());
    }

    #[Test]
    public function puede_crear_job_sin_progreso(): void
    {
        $job = new ValidarSofiaJob(1, 1, null);

        $this->assertInstanceOf(ValidarSofiaJob::class, $job);
        $this->assertEquals(1, $job->getComplementarioId());
        $this->assertNull($job->getProgressId());
    }

    #[Test]
    public function ejecuta_job_y_procesa_aspirantes(): void
    {
        // Crear modelos manualmente para evitar dependencias de factories complejos
        $programa = ComplementarioOfertado::create([
            'codigo' => 'TEST001',
            'nombre' => 'Programa Test',
            'descripcion' => 'Descripción test',
            'estado' => 1,
            'duracion' => 30,
            'cupos' => 50,
            'modalidad_id' => 1,
            'jornada_id' => 1,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(30),
        ]);

        $persona = Persona::create([
            'tipo_documento' => 1,
            'numero_documento' => '123456789',
            'primer_nombre' => 'Test',
            'segundo_nombre' => '',
            'primer_apellido' => 'User',
            'segundo_apellido' => '',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 1,
            'telefono' => '1234567890',
            'celular' => '0987654321',
            'email' => 'test@example.com',
            'pais_id' => 1,
            'departamento_id' => 1,
            'municipio_id' => 1,
            'direccion' => 'Dirección test',
            'status' => 1,
            'estado_sofia' => 0,
        ]);

        $aspirante = AspiranteComplementario::create([
            'persona_id' => $persona->id,
            'complementario_id' => $programa->id,
            'estado' => 1,
        ]);

        $progress = SofiaValidationProgress::create([
            'complementario_id' => $programa->id,
            'user_id' => 1,
            'status' => 'pending',
            'total_aspirantes' => 1,
            'processed_aspirantes' => 0,
            'successful_validations' => 0,
            'failed_validations' => 0,
        ]);

        $validationServiceMock = Mockery::mock(SofiaValidationService::class);
        $processorMock = Mockery::mock(SofiaValidationProcessor::class);

        $validationServiceMock->shouldReceive('getAspirantesToValidate')
            ->once()
            ->with($programa->id)
            ->andReturn(collect([$aspirante]));

        $processorMock->shouldReceive('processBatch')
            ->once()
            ->andReturn([
                'total' => 1,
                'exitosos' => 1,
                'errores' => 0,
                'errores_detalle' => [],
            ]);

        $job = new ValidarSofiaJob($programa->id, 1, $progress->id);
        $job->handle($validationServiceMock, $processorMock);

        $progress->refresh();
        $this->assertEquals('completed', $progress->status);
    }

    #[Test]
    public function marca_progreso_como_completado_si_no_hay_aspirantes(): void
    {
        $user = $this->createTestUser();
        $programa = ComplementarioOfertado::create([
            'codigo' => 'TEST002',
            'nombre' => 'Programa Test 2',
            'descripcion' => 'Descripción test 2',
            'estado' => 1,
            'duracion' => 30,
            'cupos' => 50,
            'modalidad_id' => 1,
            'jornada_id' => 1,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(30),
        ]);

        $progress = SofiaValidationProgress::create([
            'complementario_id' => $programa->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'total_aspirantes' => 0,
            'processed_aspirantes' => 0,
            'successful_validations' => 0,
            'failed_validations' => 0,
        ]);

        $validationServiceMock = Mockery::mock(SofiaValidationService::class);
        $processorMock = Mockery::mock(SofiaValidationProcessor::class);

        $validationServiceMock->shouldReceive('getAspirantesToValidate')
            ->once()
            ->with($programa->id)
            ->andReturn(collect());

        $job = new ValidarSofiaJob($programa->id, $user->id, $progress->id);
        $job->handle($validationServiceMock, $processorMock);

        $progress->refresh();
        $this->assertEquals('completed', $progress->status);
    }

    #[Test]
    public function inicializa_progreso_al_iniciar_job(): void
    {
        $user = $this->createTestUser();
        $programa = ComplementarioOfertado::create([
            'codigo' => 'TEST003',
            'nombre' => 'Programa Test 3',
            'descripcion' => 'Descripción test 3',
            'estado' => 1,
            'duracion' => 30,
            'cupos' => 50,
            'modalidad_id' => 1,
            'jornada_id' => 1,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(30),
        ]);

        $progress = SofiaValidationProgress::create([
            'complementario_id' => $programa->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'total_aspirantes' => 0,
            'processed_aspirantes' => 0,
            'successful_validations' => 0,
            'failed_validations' => 0,
        ]);

        $validationServiceMock = Mockery::mock(SofiaValidationService::class);
        $processorMock = Mockery::mock(SofiaValidationProcessor::class);

        $validationServiceMock->shouldReceive('getAspirantesToValidate')
            ->andReturn(collect());

        $job = new ValidarSofiaJob($programa->id, $user->id, $progress->id);
        $job->handle($validationServiceMock, $processorMock);

        $progress->refresh();
        $this->assertEquals('processing', $progress->status);
        $this->assertNotNull($progress->started_at);
    }

    #[Test]
    public function marca_progreso_como_fallido_si_hay_errores(): void
    {
        $user = $this->createTestUser();
        $programa = ComplementarioOfertado::create([
            'codigo' => 'TEST004',
            'nombre' => 'Programa Test 4',
            'descripcion' => 'Descripción test 4',
            'estado' => 1,
            'duracion' => 30,
            'cupos' => 50,
            'modalidad_id' => 1,
            'jornada_id' => 1,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(30),
        ]);

        $persona = \App\Models\Persona::create([
            'tipo_documento' => 1,
            'numero_documento' => '987654321',
            'primer_nombre' => 'Test',
            'segundo_nombre' => '',
            'primer_apellido' => 'Persona',
            'segundo_apellido' => '',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 1,
            'telefono' => '1234567890',
            'celular' => '0987654321',
            'email' => 'persona@example.com',
            'pais_id' => 1,
            'departamento_id' => 1,
            'municipio_id' => 1,
            'direccion' => 'Dirección test',
            'status' => 1,
            'estado_sofia' => 0,
        ]);

        $aspirante = AspiranteComplementario::create([
            'persona_id' => $persona->id,
            'complementario_id' => $programa->id,
            'estado' => 1,
        ]);

        $progress = SofiaValidationProgress::create([
            'complementario_id' => $programa->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'total_aspirantes' => 1,
            'processed_aspirantes' => 0,
            'successful_validations' => 0,
            'failed_validations' => 0,
        ]);

        $validationServiceMock = Mockery::mock(SofiaValidationService::class);
        $processorMock = Mockery::mock(SofiaValidationProcessor::class);

        $validationServiceMock->shouldReceive('getAspirantesToValidate')
            ->andReturn(collect([$aspirante]));

        $processorMock->shouldReceive('processBatch')
            ->andReturn([
                'total' => 1,
                'exitosos' => 0,
                'errores' => 1,
                'errores_detalle' => ['Error de conexión'],
            ]);

        $job = new ValidarSofiaJob($programa->id, $user->id, $progress->id);
        $job->handle($validationServiceMock, $processorMock);

        $progress->refresh();
        $this->assertEquals('failed', $progress->status);
    }

    #[Test]
    public function maneja_job_sin_progreso(): void
    {
        $programa = ComplementarioOfertado::factory()->create();

        $validationServiceMock = Mockery::mock(SofiaValidationService::class);
        $processorMock = Mockery::mock(SofiaValidationProcessor::class);

        $validationServiceMock->shouldReceive('getAspirantesToValidate')
            ->andReturn(collect());

        $job = new ValidarSofiaJob($programa->id, 1, null);
        $job->handle($validationServiceMock, $processorMock);

        // No debería lanzar excepción
        $this->assertTrue(true);
    }
}
