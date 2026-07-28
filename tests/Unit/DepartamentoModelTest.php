<?php

namespace Tests\Unit;

use App\Models\Departamento;
use App\Models\Pais;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DepartamentoModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function puede_crear_departamento(): void
    {
        $pais = Pais::factory()->create();

        $departamento = Departamento::factory()->create([
            'pais_id' => $pais->id,
            'departamento' => 'meta',
        ]);

        $this->assertInstanceOf(Departamento::class, $departamento);
        $this->assertEquals('META', $departamento->departamento);
    }

    #[Test]
    public function tiene_relacion_con_municipios(): void
    {
        $departamento = Departamento::factory()->create();
        $municipio = \App\Models\Municipio::factory()->create([
            'departamento_id' => $departamento->id,
        ]);

        $this->assertTrue($departamento->municipios->contains($municipio));
    }
}
