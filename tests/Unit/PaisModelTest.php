<?php

namespace Tests\Unit;

use App\Models\Pais;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaisModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function puede_crear_pais(): void
    {
        $pais = Pais::factory()->create(['pais' => 'colombia']);

        $this->assertInstanceOf(Pais::class, $pais);
        $this->assertEquals('COLOMBIA', $pais->pais);
    }

    #[Test]
    public function convierte_nombre_a_mayusculas_al_guardar(): void
    {
        $pais = Pais::factory()->create(['pais' => 'peru']);

        $this->assertEquals('PERU', $pais->fresh()->pais);
    }
}
