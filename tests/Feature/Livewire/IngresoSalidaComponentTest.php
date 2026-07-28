<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;

use App\Livewire\IngresoSalidaComponent;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class IngresoSalidaComponentTest extends TestCase
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
    public function puede_montar_el_componente(): void
    {
        Livewire::actingAs($this->user)
            ->test(IngresoSalidaComponent::class)
            ->assertStatus(200);
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
