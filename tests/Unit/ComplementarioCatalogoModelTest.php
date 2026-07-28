<?php

namespace Tests\Unit;

use App\Models\Complementarios\ComplementarioCatalogo;
use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Tema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ComplementarioCatalogoModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function puede_crear_catalogo(): void
    {
        $catalogo = ComplementarioCatalogo::query()->create([
            'prf_codigo' => 'PRF9001',
            'version' => 1,
            'cod_ver' => 'PRF9001-1',
            'denominacion' => 'Curso de Prueba Catálogo',
            'nivel_formacion' => 'CURSO ESPECIAL',
            'duracion_horas' => 40,
            'activo' => true,
        ]);

        $this->assertInstanceOf(ComplementarioCatalogo::class, $catalogo);
        $this->assertDatabaseHas('complementarios_catalogo', [
            'id' => $catalogo->id,
            'prf_codigo' => 'PRF9001',
            'denominacion' => 'Curso de Prueba Catálogo',
        ]);
    }

    #[Test]
    public function tiene_relacion_con_modalidad(): void
    {
        $tema = Tema::query()->firstOrCreate(
            ['name' => 'MODALIDADES DE FORMACION'],
            ['status' => 1]
        );
        $parametro = Parametro::query()->firstOrCreate(
            ['name' => 'PRESENCIAL'],
            ['status' => 1]
        );
        $modalidad = ParametroTema::query()->firstOrCreate(
            ['tema_id' => $tema->id, 'parametro_id' => $parametro->id],
            ['status' => 1]
        );

        $catalogo = ComplementarioCatalogo::query()->create([
            'prf_codigo' => 'PRF9002',
            'version' => 1,
            'cod_ver' => 'PRF9002-1',
            'denominacion' => 'Curso con Modalidad',
            'nivel_formacion' => 'CURSO ESPECIAL',
            'duracion_horas' => 60,
            'modalidad_id' => $modalidad->id,
            'activo' => true,
        ]);

        $this->assertEquals($modalidad->id, $catalogo->modalidad->id);
        $this->assertEquals('PRESENCIAL', $catalogo->modalidad_nombre);
    }
}
