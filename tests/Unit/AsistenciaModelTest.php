<?php

namespace Tests\Unit;

use App\Models\Asistencia;
use App\Models\Evidencias;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AsistenciaModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\ParametroSeeder::class,
            \Database\Seeders\PaisSeeder::class,
            \Database\Seeders\DepartamentoSeeder::class,
            \Database\Seeders\MunicipioSeeder::class,
        ]);
    }

    #[Test]
    public function puede_crear_asistencia(): void
    {
        $user = User::factory()->create();
        $instructorFicha = InstructorFichaCaracterizacion::factory()->create();

        $asistencia = Asistencia::create([
            'instructor_ficha_id' => $instructorFicha->id,
            'fecha' => now()->toDateString(),
            'hora_inicio' => now(),
            'is_finished' => false,
            'user_create_id' => $user->id,
            'user_edit_id' => $user->id,
        ]);

        $this->assertInstanceOf(Asistencia::class, $asistencia);
        $this->assertDatabaseHas('asistencias', [
            'id' => $asistencia->id,
            'instructor_ficha_id' => $instructorFicha->id,
            'is_finished' => 0,
        ]);
    }

    #[Test]
    public function tiene_relacion_con_instructor_ficha(): void
    {
        $instructorFicha = InstructorFichaCaracterizacion::factory()->create();
        $asistencia = Asistencia::create([
            'instructor_ficha_id' => $instructorFicha->id,
            'fecha' => now()->toDateString(),
            'hora_inicio' => now(),
            'is_finished' => false,
        ]);

        $this->assertEquals($instructorFicha->id, $asistencia->instructorFicha->id);
    }

    #[Test]
    public function tiene_relacion_con_evidencia(): void
    {
        $evidencia = Evidencias::factory()->create();
        $instructorFicha = InstructorFichaCaracterizacion::factory()->create();

        $asistencia = Asistencia::create([
            'evidencia_id' => $evidencia->id,
            'instructor_ficha_id' => $instructorFicha->id,
            'fecha' => now()->toDateString(),
            'hora_inicio' => now(),
            'is_finished' => false,
        ]);

        $this->assertInstanceOf(Evidencias::class, $asistencia->evidencia);
        $this->assertEquals($evidencia->id, $asistencia->evidencia->id);
    }
}
