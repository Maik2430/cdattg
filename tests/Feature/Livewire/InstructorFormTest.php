<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;

use App\Livewire\Instructores\InstructorForm;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class InstructorFormTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RolePermissionSeeder::class]);

        $this->user = User::factory()->create();
        $this->user->assignRole('SUPER ADMINISTRADOR');
    }

    #[Test]
    public function can_render(): void
    {
        Livewire::actingAs($this->user)
            ->test(InstructorForm::class)
            ->assertStatus(200);
    }
}
