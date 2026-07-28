<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonaIngresoSalidaControllerTest extends TestCase
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
    public function puede_obtener_estadisticas_personas_dentro(): void
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/presencia/estadisticas');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    #[Test]
    public function puede_obtener_estadisticas_de_hoy(): void
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/presencia/estadisticas/hoy');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    #[Test]
    public function puede_obtener_personas_dentro(): void
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/api/presencia/personas-dentro');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    #[Test]
    public function endpoints_son_publicos_sin_autenticacion(): void
    {
        $response = $this->getJson('/api/presencia/estadisticas');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    /**
     * Tabla documentada en migrations_batches pero sin migration en el repo;
     * se crea aquí solo para tests Feature del controller.
     */
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
