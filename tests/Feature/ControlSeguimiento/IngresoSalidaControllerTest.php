<?php

namespace Tests\Feature\ControlSeguimiento;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IngresoSalidaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);

        $this->ensurePersonaIngresoSalidaTable();

        $this->user = User::factory()->create();
    }

    #[Test]
    public function puede_ver_dashboard_ingreso_salida(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('control-seguimiento.ingreso-salida.index'));

        $response->assertStatus(200);
        $response->assertViewIs('control-seguimiento.ingreso-salida.index');
    }

    #[Test]
    public function puede_ver_formulario_registro(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('control-seguimiento.ingreso-salida.create'));

        $response->assertStatus(200);
        $response->assertViewIs('control-seguimiento.ingreso-salida.create');
    }

    #[Test]
    public function requiere_autenticacion(): void
    {
        $response = $this->get(route('control-seguimiento.ingreso-salida.index'));

        $response->assertRedirect(route('verificarLogin'));
    }

    private function ensurePersonaIngresoSalidaTable(): void
    {
        if (Schema::hasTable('persona_ingreso_salida')) {
            return;
        }

        Schema::create('persona_ingreso_salida', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('persona_id');
            $table->unsignedBigInteger('sede_id');
            $table->string('tipo_persona')->nullable();
            $table->date('fecha_entrada')->nullable();
            $table->time('hora_entrada')->nullable();
            $table->timestamp('timestamp_entrada')->nullable();
            $table->date('fecha_salida')->nullable();
            $table->time('hora_salida')->nullable();
            $table->timestamp('timestamp_salida')->nullable();
            $table->unsignedBigInteger('ambiente_id')->nullable();
            $table->unsignedBigInteger('ficha_caracterizacion_id')->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('user_create_id')->nullable();
            $table->unsignedBigInteger('user_edit_id')->nullable();
            $table->timestamps();
        });
    }
}
