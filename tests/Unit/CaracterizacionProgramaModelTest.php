<?php

namespace Tests\Unit;

use App\Models\CaracterizacionPrograma;
use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\ProgramaFormacion;
use App\Models\Tema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CaracterizacionProgramaModelTest extends TestCase
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
    public function puede_crear_y_relacionar_ficha_programa_y_jornada(): void
    {
        $ficha = FichaCaracterizacion::factory()->create();
        $programa = ProgramaFormacion::factory()->create();
        $instructor = Instructor::factory()->create();

        $tema = Tema::query()->firstOrCreate(['name' => 'JORNADAS'], ['status' => 1]);
        $parametro = Parametro::query()->firstOrCreate(['name' => 'DIURNA'], ['status' => 1]);
        $jornada = ParametroTema::query()->firstOrCreate(
            ['tema_id' => $tema->id, 'parametro_id' => $parametro->id],
            ['status' => 1]
        );

        $registro = CaracterizacionPrograma::query()->create([
            'ficha_id' => $ficha->id,
            'programa_formacion_id' => $programa->id,
            'instructor_id' => $instructor->id,
            'jornada_id' => $jornada->id,
        ]);

        $this->assertInstanceOf(CaracterizacionPrograma::class, $registro);
        $this->assertEquals($ficha->id, $registro->ficha->id);
        $this->assertEquals($programa->id, $registro->programaFormacion->id);
        $this->assertEquals($jornada->id, $registro->jornada->id);
        $this->assertInstanceOf(ParametroTema::class, $registro->jornada);
    }
}
