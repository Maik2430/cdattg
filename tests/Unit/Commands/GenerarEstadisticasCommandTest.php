<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\GenerarEstadisticasCommand;
use App\Services\EstadisticasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GenerarEstadisticasCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function command_existe(): void
    {
        $this->mock(EstadisticasService::class, function ($mock): void {
            $mock->shouldReceive('obtenerDashboardGeneral')->once()->andReturn([
                'aprendices' => ['total' => 0, 'activos' => 0],
                'fichas' => ['total' => 0, 'vigentes' => 0],
                'instructores' => 0,
                'asistencias_hoy' => 0,
            ]);
        });

        $this->artisan('estadisticas:generar')
            ->assertExitCode(0);
    }

    #[Test]
    public function tiene_signature_correcto(): void
    {
        $command = new GenerarEstadisticasCommand(app(EstadisticasService::class));

        $this->assertEquals('estadisticas:generar', $command->getName());
    }
}
