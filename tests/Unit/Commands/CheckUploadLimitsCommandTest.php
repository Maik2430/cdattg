<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\CheckUploadLimitsCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckUploadLimitsCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function command_existe(): void
    {
        $exitCode = \Illuminate\Support\Facades\Artisan::call('upload:check-limits');

        $this->assertContains($exitCode, [0, 1]);
        $this->assertStringContainsString(
            'Verificando configuración de límites de carga',
            \Illuminate\Support\Facades\Artisan::output()
        );
    }

    #[Test]
    public function tiene_signature_correcto(): void
    {
        $command = new CheckUploadLimitsCommand;

        $this->assertEquals('upload:check-limits', $command->getName());
    }
}
