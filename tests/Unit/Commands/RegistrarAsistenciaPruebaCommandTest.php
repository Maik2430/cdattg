<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\RegistrarAsistenciaPrueba;
use App\Events\NuevaAsistenciaRegistrada;
use App\Models\Aprendiz;
use App\Models\AsistenciaAprendiz;
use App\Models\FichaCaracterizacion;
use App\Models\InstructorFichaCaracterizacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrarAsistenciaPruebaCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\ParametroSeeder::class,
            \Database\Seeders\TemaSeeder::class,
            \Database\Seeders\PaisSeeder::class,
            \Database\Seeders\DepartamentoSeeder::class,
            \Database\Seeders\MunicipioSeeder::class,
        ]);
    }

    #[Test]
    public function command_existe(): void
    {
        $command = new RegistrarAsistenciaPrueba;

        $this->assertEquals(
            'asistencia:registrar',
            $command->getName()
        );
    }

    #[Test]
    public function valida_tipo_invalido(): void
    {
        $exitCode = Artisan::call('asistencia:registrar', ['tipo' => 'invalido']);

        $this->assertEquals(1, $exitCode);

        $output = Artisan::output();
        $this->assertStringContainsString('Tipo no válido', $output);
    }

    #[Test]
    public function muestra_error_si_no_hay_aprendices(): void
    {
        $exitCode = Artisan::call('asistencia:registrar', ['tipo' => 'entrada']);

        $this->assertEquals(1, $exitCode);

        $output = Artisan::output();
        $this->assertStringContainsString('No se encontró ningún aprendiz', $output);
    }

    #[Test]
    public function muestra_error_si_no_hay_instructores_asignados(): void
    {
        $ficha = FichaCaracterizacion::factory()->create();
        Aprendiz::factory()->create(['ficha_caracterizacion_id' => $ficha->id]);

        $exitCode = Artisan::call('asistencia:registrar', ['tipo' => 'entrada']);

        $this->assertEquals(1, $exitCode);

        $output = Artisan::output();
        $this->assertStringContainsString('No se encontró ningún instructor asignado', $output);
    }

    #[Test]
    public function registra_asistencia_entrada(): void
    {
        Event::fake([NuevaAsistenciaRegistrada::class]);

        $ficha = FichaCaracterizacion::factory()->create();
        $aprendiz = Aprendiz::factory()->create(['ficha_caracterizacion_id' => $ficha->id]);
        InstructorFichaCaracterizacion::factory()->create(['ficha_id' => $ficha->id]);

        $exitCode = Artisan::call('asistencia:registrar', ['tipo' => 'entrada']);

        $output = Artisan::output();
        $this->assertStringContainsString('Asistencia de ENTRADA registrada', $output);
        $this->assertEquals(0, $exitCode);

        $this->assertDatabaseHas('asistencia_aprendices', [
            'aprendiz_ficha_id' => $aprendiz->id,
        ]);

        Event::assertDispatched(NuevaAsistenciaRegistrada::class);
    }

    #[Test]
    public function muestra_error_salida_sin_entrada(): void
    {
        $ficha = FichaCaracterizacion::factory()->create();
        Aprendiz::factory()->create(['ficha_caracterizacion_id' => $ficha->id]);
        InstructorFichaCaracterizacion::factory()->create(['ficha_id' => $ficha->id]);

        $exitCode = Artisan::call('asistencia:registrar', ['tipo' => 'salida']);

        $this->assertEquals(1, $exitCode);

        $output = Artisan::output();
        $this->assertStringContainsString('No se encontró una asistencia de entrada', $output);
    }

    #[Test]
    public function registra_asistencia_salida(): void
    {
        Event::fake([NuevaAsistenciaRegistrada::class]);

        $ficha = FichaCaracterizacion::factory()->create();
        $aprendiz = Aprendiz::factory()->create(['ficha_caracterizacion_id' => $ficha->id]);
        $instructorFicha = InstructorFichaCaracterizacion::factory()->create(['ficha_id' => $ficha->id]);

        AsistenciaAprendiz::query()->create([
            'instructor_ficha_id' => $instructorFicha->id,
            'aprendiz_ficha_id' => $aprendiz->id,
            'hora_ingreso' => now()->format('H:i:s'),
            'hora_salida' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $exitCode = Artisan::call('asistencia:registrar', ['tipo' => 'salida']);

        $output = Artisan::output();
        $this->assertStringContainsString('Asistencia de SALIDA registrada', $output);
        $this->assertEquals(0, $exitCode);

        Event::assertDispatched(NuevaAsistenciaRegistrada::class);
    }
}
