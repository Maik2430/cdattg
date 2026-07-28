<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\RefactorSonarQubeCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RefactorSonarQubeCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function command_existe(): void
    {
        $command = new RefactorSonarQubeCommand;

        $this->assertEquals(
            'refactor:sonarqube',
            $command->getName()
        );
    }

    #[Test]
    public function ejecuta_en_entorno_produccion_sin_romper_flujo(): void
    {
        config(['app.env' => 'production']);

        $exitCode = Artisan::call('refactor:sonarqube', ['--path' => 'app']);

        $output = Artisan::output();
        $this->assertStringContainsString('REPORTE FINAL', $output);
        $this->assertEquals(0, $exitCode);
    }

    #[Test]
    public function ejecuta_en_modo_dry_run(): void
    {
        config(['app.env' => 'testing']);

        $exitCode = Artisan::call('refactor:sonarqube', [
            '--path' => 'app',
            '--dry-run' => true,
        ]);

        $output = Artisan::output();
        $this->assertStringContainsString('DRY-RUN', $output);
        $this->assertEquals(0, $exitCode);
    }

    #[Test]
    public function muestra_error_si_ruta_no_existe(): void
    {
        config(['app.env' => 'testing']);

        $exitCode = Artisan::call('refactor:sonarqube', [
            '--path' => 'ruta/inexistente',
        ]);

        $output = Artisan::output();
        $this->assertStringContainsString('no existe', $output);
        $this->assertEquals(1, $exitCode);
    }
}
