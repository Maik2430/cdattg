<?php

namespace Tests\Feature\Livewire\Aprendices;

use App\Livewire\Aprendices\AprendizForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AprendizFormTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);

        $this->user = User::factory()->create();
    }

    #[Test]
    public function puede_montar_el_componente(): void
    {
        Livewire::actingAs($this->user)
            ->test(AprendizForm::class)
            ->assertStatus(200);
    }
}
