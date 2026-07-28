<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;

use App\Livewire\RedConocimiento\RedConocimientoForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class RedConocimientoFormTest extends TestCase
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
    public function puede_montar_red_conocimiento_form(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(RedConocimientoForm::class)
            ->assertStatus(200);
    }
}
