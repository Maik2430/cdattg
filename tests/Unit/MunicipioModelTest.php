<?php

namespace Tests\Unit;

use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MunicipioModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function puede_crear_municipio(): void
    {
        $departamento = Departamento::factory()->create();

        $municipio = Municipio::factory()->create([
            'departamento_id' => $departamento->id,
            'municipio' => 'villavicencio',
        ]);

        $this->assertInstanceOf(Municipio::class, $municipio);
        $this->assertEquals('VILLAVICENCIO', $municipio->municipio);
    }

    #[Test]
    public function tiene_relacion_con_departamento(): void
    {
        $departamento = Departamento::factory()->create();
        $municipio = Municipio::factory()->create([
            'departamento_id' => $departamento->id,
        ]);

        $this->assertNotNull($municipio->departamento);
        $this->assertEquals($departamento->id, $municipio->departamento->id);
    }
}
