<?php

namespace Tests\Unit;

use App\Models\Ambiente;
use App\Models\Bloque;
use App\Models\ParametroTema;
use App\Models\Piso;
use App\Models\Sede;
use App\Services\FichaCaracterizacionValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FichaCaracterizacionValidationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FichaCaracterizacionValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(FichaCaracterizacionValidationService::class);
    }

    /**
     * Crea un Ambiente con dependencias mínimas (país → depto → municipio → sede → bloque → piso).
     */
    private function ensureAmbiente(): Ambiente
    {
        $ambiente = Ambiente::first();
        if ($ambiente) {
            return $ambiente;
        }

        $pais = \App\Models\Pais::first() ?? \App\Models\Pais::create([
            'pais' => 'COLOMBIA',
            'status' => 1,
        ]);

        $departamento = \App\Models\Departamento::first() ?? \App\Models\Departamento::create([
            'departamento' => 'CUNDINAMARCA',
            'pais_id' => $pais->id,
            'status' => 1,
        ]);

        $municipio = \App\Models\Municipio::first() ?? \App\Models\Municipio::create([
            'municipio' => 'BOGOTA',
            'departamento_id' => $departamento->id,
            'status' => 1,
        ]);

        $user = \App\Models\User::factory()->create();

        $regional = \App\Models\Regional::first() ?? \App\Models\Regional::create([
            'nombre' => 'REGIONAL TEST',
            'departamento_id' => $departamento->id,
            'status' => 1,
            'user_create_id' => $user->id,
            'user_edit_id' => $user->id,
        ]);

        $sede = Sede::first() ?? Sede::create([
            'sede' => 'SEDE TEST',
            'direccion' => 'Calle Test',
            'municipio_id' => $municipio->id,
            'regional_id' => $regional->id,
            'status' => 1,
            'user_create_id' => $user->id,
            'user_edit_id' => $user->id,
        ]);

        $bloque = Bloque::first() ?? Bloque::create([
            'bloque' => 'B1',
            'sede_id' => $sede->id,
            'status' => 1,
            'user_create_id' => $user->id,
            'user_edit_id' => $user->id,
        ]);

        $piso = Piso::first() ?? Piso::create([
            'piso' => 'P1',
            'bloque_id' => $bloque->id,
            'status' => 1,
            'user_create_id' => $user->id,
            'user_edit_id' => $user->id,
        ]);

        return Ambiente::create([
            'title' => 'Ambiente Test',
            'piso_id' => $piso->id,
            'status' => 1,
            'user_create_id' => $user->id,
            'user_edit_id' => $user->id,
        ]);
    }

    #[Test]
    public function puede_validar_ficha_completa(): void
    {
        $ambiente = $this->ensureAmbiente();
        $jornada = ParametroTema::query()
            ->whereHas('tema', fn ($q) => $q->where('name', 'LIKE', '%JORNADA%'))
            ->first();

        if (! $jornada) {
            $tema = \App\Models\Tema::firstOrCreate(['name' => 'JORNADAS'], ['status' => 1]);
            $parametro = \App\Models\Parametro::firstOrCreate(['name' => 'DIURNA'], ['status' => 1]);
            $jornada = ParametroTema::firstOrCreate(
                ['tema_id' => $tema->id, 'parametro_id' => $parametro->id],
                ['status' => 1]
            );
        }

        $datos = [
            'ficha' => '123456',
            'programa_formacion_id' => 1,
            'ambiente_id' => $ambiente->id,
            'fecha_inicio' => now()->addMonth()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(7)->format('Y-m-d'),
            'jornada_id' => $jornada->id,
        ];

        $resultado = $this->service->validarFichaCompleta($datos);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('valido', $resultado);
        $this->assertArrayHasKey('errores', $resultado);
        $this->assertArrayHasKey('advertencias', $resultado);
    }

    #[Test]
    public function valida_disponibilidad_ambiente(): void
    {
        $ambiente = $this->ensureAmbiente();

        $datos = [
            'ambiente_id' => $ambiente->id,
            'fecha_inicio' => now()->addMonth()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(7)->format('Y-m-d'),
            'cupo' => 20,
            'programa_formacion_id' => 1,
        ];

        $resultado = $this->service->validarFichaCompleta($datos);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('valido', $resultado);
        $this->assertArrayHasKey('errores', $resultado);
    }
}
