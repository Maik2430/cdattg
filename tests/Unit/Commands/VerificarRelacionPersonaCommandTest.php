<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\VerificarRelacionPersona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VerificarRelacionPersonaCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function command_existe(): void
    {
        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);

        $exitCode = \Illuminate\Support\Facades\Artisan::call('aprendices:verificar-relacion-persona');

        $this->assertEquals(1, $exitCode);
        $this->assertStringContainsString(
            'No hay aprendices en la base de datos',
            \Illuminate\Support\Facades\Artisan::output()
        );
    }

    #[Test]
    public function tiene_signature_correcto(): void
    {
        $command = new VerificarRelacionPersona;

        $this->assertEquals('aprendices:verificar-relacion-persona', $command->getName());
    }
}
