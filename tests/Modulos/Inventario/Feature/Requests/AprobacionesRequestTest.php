<?php

declare(strict_types=1);

namespace Tests\Inventario\Feature\Request;

use Tests\TestCase;
use App\Http\Requests\Inventario\AprobacionesRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;

class AprobacionesRequestTest extends TestCase
{
    // No necesitamos RefreshDatabase para validar reglas de FormRequest
    // use RefreshDatabase;

    private const LONGITUD_MAX_MOTIVO = 1000;
    private const LONGITUD_INVALIDA_MOTIVO = 1001;

    private function obtenerRules(): array
    {
        // Crear una instancia del request con un Request mock
        $request = new AprobacionesRequest();
        // Los FormRequest pueden necesitar el Request actual, pero para obtener solo las reglas esto debería funcionar
        return $request->rules();
    }

    private function validarYVerificarError(array $data, array $rules, string $campoEsperado): void
    {
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey($campoEsperado, $validator->errors()->toArray());
    }

    #[Test]
    public function valida_motivo_rechazo_requerido(): void
    {
        $rules = $this->obtenerRules();

        $this->validarYVerificarError([], $rules, 'motivo_rechazo');
    }

    #[Test]
    public function valida_motivo_rechazo_debe_ser_string(): void
    {
        $rules = $this->obtenerRules();

        $this->validarYVerificarError(
            ['motivo_rechazo' => 123],
            $rules,
            'motivo_rechazo'
        );
    }

    #[Test]
    public function valida_longitud_maxima_de_motivo_rechazo(): void
    {
        $rules = $this->obtenerRules();

        $this->validarYVerificarError(
            ['motivo_rechazo' => str_repeat('a', self::LONGITUD_INVALIDA_MOTIVO)],
            $rules,
            'motivo_rechazo'
        );
    }

    #[Test]
    public function acepta_motivo_rechazo_valido(): void
    {
        $rules = $this->obtenerRules();

        $validator = Validator::make(
            ['motivo_rechazo' => 'Motivo de rechazo válido'],
            $rules
        );

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function acepta_motivo_rechazo_con_longitud_maxima(): void
    {
        $rules = $this->obtenerRules();

        $validator = Validator::make(
            ['motivo_rechazo' => str_repeat('a', self::LONGITUD_MAX_MOTIVO)],
            $rules
        );

        $this->assertFalse($validator->fails());
    }
}

