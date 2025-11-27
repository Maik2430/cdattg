<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\FichaService;
use App\Repositories\FichaRepository;
use App\Repositories\InstructorFichaRepository;
use App\Repositories\AprendizFichaRepository;
use App\Models\FichaCaracterizacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class FichaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FichaService $service;
    protected $mockFichaRepo;
    protected $mockInstructorFichaRepo;
    protected $mockAprendizFichaRepo;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockFichaRepo = Mockery::mock(FichaRepository::class);
        $this->mockInstructorFichaRepo = Mockery::mock(InstructorFichaRepository::class);
        $this->mockAprendizFichaRepo = Mockery::mock(AprendizFichaRepository::class);
        
        $this->service = new FichaService(
            $this->mockFichaRepo,
            $this->mockInstructorFichaRepo,
            $this->mockAprendizFichaRepo
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function puede_obtener_estadisticas()
    {
        $estadisticasEsperadas = [
            'total' => 100,
            'activas' => 80,
            'vigentes' => 60,
        ];

        $this->mockFichaRepo
            ->shouldReceive('obtenerEstadisticas')
            ->once()
            ->andReturn($estadisticasEsperadas);

        $resultado = $this->service->obtenerEstadisticas();

        $this->assertEquals($estadisticasEsperadas, $resultado);
    }

    #[Test]
    public function puede_verificar_disponibilidad()
    {
        $fichaId = 1;

        $fichaMock = Mockery::mock(FichaCaracterizacion::class);
        $fichaMock->id = $fichaId;
        $fichaMock->status = true;
        $fichaMock->cupos_maximos = 40;
        
        $this->mockFichaRepo
            ->shouldReceive('encontrarConRelaciones')
            ->once()
            ->andReturn($fichaMock);

        $this->mockInstructorFichaRepo
            ->shouldReceive('obtenerPorFicha')
            ->once()
            ->andReturn(collect([]));

        $this->mockAprendizFichaRepo
            ->shouldReceive('obtenerPorFicha')
            ->once()
            ->andReturn(collect([]));

        $resultado = $this->service->verificarDisponibilidad($fichaId);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('disponible', $resultado);
        $this->assertArrayHasKey('total_instructores', $resultado);
        $this->assertArrayHasKey('total_aprendices', $resultado);
    }
}

