<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Asistencia\CrearEvidenciaModal;
use App\Models\Asistencia;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CrearEvidenciaModalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

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

        $this->user = User::factory()->create();
        $this->user->assignRole('SUPER ADMINISTRADOR');
    }

    #[Test]
    public function puede_montar_crear_evidencia_modal(): void
    {
        Livewire::actingAs($this->user)
            ->test(CrearEvidenciaModal::class)
            ->assertStatus(200);
    }

    #[Test]
    public function puede_crear_evidencia_y_asistencia(): void
    {
        $instructorFicha = InstructorFichaCaracterizacion::factory()->create();
        $nombre = 'Evidencia Livewire '.uniqid();

        $component = Livewire::actingAs($this->user)
            ->test(CrearEvidenciaModal::class)
            ->set('nombreEvidencia', $nombre)
            ->set('selectedFichaId', $instructorFicha->id)
            ->call('crearEvidencia');

        $this->assertDatabaseHas('evidencias', [
            'nombre' => $nombre,
            'user_create_id' => $this->user->id,
        ]);

        $asistencia = Asistencia::query()
            ->where('instructor_ficha_id', $instructorFicha->id)
            ->where('user_create_id', $this->user->id)
            ->first();

        $this->assertNotNull($asistencia);
        $this->assertFalse((bool) $asistencia->is_finished);

        $component->assertRedirect(route('asistence.caracterSelected', [
            'caracterizacion' => $instructorFicha->id,
            'asistencia_id' => $asistencia->id,
        ]));
    }
}
