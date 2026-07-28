<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;

use App\Livewire\PersonaImportComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class PersonaImportComponentTest extends TestCase
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
    public function puede_montar_persona_import_component(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PersonaImportComponent::class)
            ->assertStatus(200);
    }
}
