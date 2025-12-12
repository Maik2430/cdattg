<?php

declare(strict_types=1);

namespace Tests\Modulos\Inventario\Unit\Repositories;

use App\Inventario\Repositories\ParametroTema\ParametroTemaRepository;
use App\Models\Tema;
use App\Models\Parametro;
use App\Models\ParametroTema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ParametroTemaRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private const TEMA_TIPOS_PRODUCTO = 'TIPOS DE PRODUCTO';
    private const TEMA_ESTADOS_PRODUCTO = 'ESTADOS DE PRODUCTO';
    private const TEMA_ESTADOS_ORDEN = 'ESTADOS DE ORDEN';
    private const PARAMETRO_NO_CONSUMIBLE = 'NO CONSUMIBLE';
    private const PARAMETRO_CONSUMIBLE = 'CONSUMIBLE';
    private const PARAMETRO_DISPONIBLE = 'DISPONIBLE';
    private const PARAMETRO_APROBADA = 'APROBADA';
    private const PARAMETRO_RECHAZADA = 'RECHAZADA';
    private const PARAMETRO_EN_ESPERA = 'TEMA INEXISTENTE';
    private const PARAMETRO_MARCAS = 'MARCAS';
    private const PARAMETRO_CATEGORIAS = 'CATEGORIAS';

    private ParametroTemaRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ParametroTemaRepository();
    }

    #[test]
    public function obtiene_parametros_tema_por_nombre_de_tema(): void
    {
        // Arrange
        $tema = Tema::factory()->create(['name' => self::TEMA_TIPOS_PRODUCTO]);
        $parametro1 = Parametro::factory()->create(['name' => self::PARAMETRO_CONSUMIBLE]);
        $parametro2 = Parametro::factory()->create(['name' => self::PARAMETRO_NO_CONSUMIBLE]);
        
        $parametroTema1 = ParametroTema::create([
            'tema_id' => $tema->id,
            'parametro_id' => $parametro1->id,
            'status' => 1
        ]);
        
        $parametroTema2 = ParametroTema::create([
            'tema_id' => $tema->id,
            'parametro_id' => $parametro2->id,
            'status' => 1
        ]);

        // Act
        $resultado = $this->repository->obtenerPorTema(self::TEMA_TIPOS_PRODUCTO);

        // Assert
        $this->assertCount(2, $resultado);
        $this->assertEquals($parametroTema1->id, $resultado->first()->id);
        $this->assertEquals(self::PARAMETRO_CONSUMIBLE, $resultado->first()->parametro->name);
        $this->assertEquals($parametroTema2->id, $resultado->last()->id);
        $this->assertEquals(self::PARAMETRO_NO_CONSUMIBLE, $resultado->last()->parametro->name);
    }

    #[test]
    public function retorna_coleccion_vacia_cuando_tema_no_existe(): void
    {
        // Act
        $resultado = $this->repository->obtenerPorTema(self::PARAMETRO_EN_ESPERA);

        // Assert
        $this->assertCount(0, $resultado);
        $this->assertTrue($resultado->isEmpty());
    }

    #[test]
    public function no_obtiene_parametros_tema_inactivos(): void
    {
        // Arrange
        $tema = Tema::factory()->create(['name' => self::TEMA_TIPOS_PRODUCTO]);
        $parametroActivo = Parametro::factory()->create(['name' => self::PARAMETRO_CONSUMIBLE]);
        $parametroInactivo = Parametro::factory()->create(['name' => self::PARAMETRO_NO_CONSUMIBLE]);
        
        ParametroTema::create([
            'tema_id' => $tema->id,
            'parametro_id' => $parametroActivo->id,
            'status' => 1
        ]);
        
        ParametroTema::create([
            'tema_id' => $tema->id,
            'parametro_id' => $parametroInactivo->id,
            'status' => 0
        ]);

        // Act
        $resultado = $this->repository->obtenerPorTema(self::TEMA_TIPOS_PRODUCTO);

        // Assert
        $this->assertCount(1, $resultado);
        $this->assertEquals(self::PARAMETRO_CONSUMIBLE, $resultado->first()->parametro->name);
    }

    #[test]
    public function obtiene_parametro_tema_por_tema_y_parametro(): void
    {
        // Arrange
        $tema = Tema::factory()->create(['name' => self::TEMA_ESTADOS_PRODUCTO]);
        $parametro = Parametro::factory()->create(['name' => self::PARAMETRO_DISPONIBLE]);
        
        $parametroTema = ParametroTema::create([
            'tema_id' => $tema->id,
            'parametro_id' => $parametro->id,
            'status' => 1
        ]);

        // Act
        $resultado = $this->repository->obtenerPorTemaYParametro($tema->id, $parametro->id);

        // Assert
        $this->assertNotNull($resultado);
        $this->assertEquals($parametroTema->id, $resultado->id);
        $this->assertEquals($tema->id, $resultado->tema_id);
        $this->assertEquals($parametro->id, $resultado->parametro_id);
    }

    #[test]
    public function retorna_null_cuando_parametro_tema_no_existe(): void
    {
        // Arrange
        Tema::factory()->create(['name' => self::TEMA_ESTADOS_PRODUCTO]);
        Parametro::factory()->create(['name' => self::PARAMETRO_DISPONIBLE]);

        // Act - Buscamos con IDs que no tienen relación
        $resultado = $this->repository->obtenerPorTemaYParametro(9999, 9999);

        // Assert
        $this->assertNull($resultado);
    }

    #[test]
    public function no_obtiene_parametro_tema_inactivo_por_tema_y_parametro(): void
    {
        // Arrange
        $tema = Tema::factory()->create(['name' => self::TEMA_ESTADOS_PRODUCTO]);
        $parametro = Parametro::factory()->create(['name' => self::PARAMETRO_DISPONIBLE]);
        
        ParametroTema::create([
            'tema_id' => $tema->id,
            'parametro_id' => $parametro->id,
            'status' => 0 // Inactivo
        ]);

        // Act
        $resultado = $this->repository->obtenerPorTemaYParametro($tema->id, $parametro->id);

        // Assert
        $this->assertNull($resultado);
    }

    #[test]
    public function obtiene_estado_por_nombre(): void
    {
        // Arrange
        $tema = Tema::factory()->create(['name' => self::TEMA_ESTADOS_ORDEN]);
        $parametro = Parametro::factory()->create(['name' => self::PARAMETRO_APROBADA]);
        
        $parametroTema = ParametroTema::create([
            'tema_id' => $tema->id,
            'parametro_id' => $parametro->id,
            'status' => 1
        ]);

        // Act
        $resultado = $this->repository->obtenerEstadoPorNombre(self::PARAMETRO_APROBADA, self::TEMA_ESTADOS_ORDEN);

        // Assert
        $this->assertNotNull($resultado);
        $this->assertEquals($parametroTema->id, $resultado->id);
        $this->assertEquals(self::PARAMETRO_APROBADA, $resultado->parametro->name);
    }

    #[test]
    public function retorna_null_cuando_tema_no_existe_al_buscar_estado(): void
    {
        // Arrange
        Parametro::factory()->create(['name' => self::PARAMETRO_APROBADA]);

        // Act
        $resultado = $this->repository->obtenerEstadoPorNombre(self::PARAMETRO_APROBADA, self::PARAMETRO_EN_ESPERA);

        // Assert
        $this->assertNull($resultado);
    }

    #[test]
    public function retorna_null_cuando_parametro_no_existe_al_buscar_estado(): void
    {
        // Arrange
        Tema::factory()->create(['name' => self::TEMA_ESTADOS_ORDEN]);

        // Act
        $resultado = $this->repository->obtenerEstadoPorNombre('PARAMETRO INEXISTENTE', self::TEMA_ESTADOS_ORDEN);

        // Assert
        $this->assertNull($resultado);
    }

    #[test]
    public function no_obtiene_estado_inactivo_por_nombre(): void
    {
        // Arrange
        $tema = Tema::factory()->create(['name' => self::TEMA_ESTADOS_ORDEN]);
        $parametro = Parametro::factory()->create(['name' => self::PARAMETRO_RECHAZADA]);
        
        ParametroTema::create([
            'tema_id' => $tema->id,
            'parametro_id' => $parametro->id,
            'status' => 0 // Inactivo
        ]);

        // Act
        $resultado = $this->repository->obtenerEstadoPorNombre(self::PARAMETRO_RECHAZADA, self::TEMA_ESTADOS_ORDEN);

        // Assert
        $this->assertNull($resultado);
    }

    #[test]
    public function obtiene_multiples_parametros_tema_del_mismo_tema(): void
    {
        // Arrange
        $tema = Tema::factory()->create(['name' => self::PARAMETRO_MARCAS]);
        $parametros = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $parametro = Parametro::factory()->create(['name' => "MARCA $i"]);
            ParametroTema::create([
                'tema_id' => $tema->id,
                'parametro_id' => $parametro->id,
                'status' => 1
            ]);
            $parametros[] = $parametro;
        }

        // Act
        $resultado = $this->repository->obtenerPorTema(self::PARAMETRO_MARCAS);

        // Assert
        $this->assertCount(5, $resultado);
        foreach ($resultado as $parametroTema) {
            $this->assertInstanceOf(ParametroTema::class, $parametroTema);
            $this->assertTrue($parametroTema->relationLoaded('parametro'));
        }
    }

    #[test]
    public function parametros_tema_tienen_relacion_parametro_cargada(): void
    {
        // Arrange
        $tema = Tema::factory()->create(['name' => self::PARAMETRO_CATEGORIAS]);
        $parametro = Parametro::factory()->create(['name' => 'ELECTRÓNICA']);
        
        ParametroTema::create([
            'tema_id' => $tema->id,
            'parametro_id' => $parametro->id,
            'status' => 1
        ]);

        // Act
        $resultado = $this->repository->obtenerPorTema(self::PARAMETRO_CATEGORIAS);

        // Assert
        $this->assertCount(1, $resultado);
        $parametroTema = $resultado->first();
        $this->assertTrue($parametroTema->relationLoaded('parametro'));
        $this->assertNotNull($parametroTema->parametro);
        $this->assertEquals('ELECTRÓNICA', $parametroTema->parametro->name);
    }
}
