<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;

use App\Livewire\ResultadosAprendizaje\ResultadoAprendizajeForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class ResultadoAprendizajeFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);
    }

    #[Test]
    public function puede_montar_resultado_aprendizaje_form(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ResultadoAprendizajeForm::class)
            ->assertStatus(200);
    }
}
