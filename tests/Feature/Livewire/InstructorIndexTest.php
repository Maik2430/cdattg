<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Instructores\InstructorIndex;
use App\Models\Instructor;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InstructorIndexTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            \Database\Seeders\ParametroSeeder::class,
            \Database\Seeders\PaisSeeder::class,
            \Database\Seeders\DepartamentoSeeder::class,
            \Database\Seeders\MunicipioSeeder::class,
        ]);

        $this->user = User::factory()->create();
        $this->user->assignRole('SUPER ADMINISTRADOR');

        if (! \App\Models\Regional::query()->exists()) {
            \App\Models\Regional::factory()->create([
                'user_create_id' => $this->user->id,
                'user_edit_id' => $this->user->id,
            ]);
        }
    }

    #[Test]
    public function can_render(): void
    {
        Livewire::actingAs($this->user)
            ->test(InstructorIndex::class)
            ->assertStatus(200);
    }

    #[Test]
    public function puede_cambiar_estado_de_instructor(): void
    {
        $instructor = Instructor::factory()->createdBy($this->user->id)->create(['status' => true]);

        Livewire::actingAs($this->user)
            ->test(InstructorIndex::class)
            ->call('toggleStatus', $instructor->id)
            ->assertDispatched('notify');

        $this->assertFalse((bool) $instructor->fresh()->status);
    }

    #[Test]
    public function puede_eliminar_instructor_sin_fichas(): void
    {
        $instructor = Instructor::factory()->createdBy($this->user->id)->create();

        Livewire::actingAs($this->user)
            ->test(InstructorIndex::class)
            ->call('deleteInstructor', $instructor->id)
            ->assertDispatched('notify');

        $this->assertDatabaseMissing('instructors', ['id' => $instructor->id]);
    }
}
