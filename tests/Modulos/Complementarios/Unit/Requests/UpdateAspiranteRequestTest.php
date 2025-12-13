<?php

declare(strict_types=1);

namespace Tests\Complementarios\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\Complementarios\UpdateAspiranteRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;

class UpdateAspiranteRequestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function valida_estado_debe_ser_entero(): void
    {
        $request = new UpdateAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'estado' => 'no-es-entero',
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('estado', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_estado_debe_ser_valor_permitido(): void
    {
        $request = new UpdateAspiranteRequest();
        $rules = $request->rules();

        // Estados permitidos: 1, 3, 4
        $validator = Validator::make([
            'estado' => 99, // Estado inválido
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('estado', $validator->errors()->toArray());
        $this->assertStringContainsString(
            'El estado debe ser: 1 (En proceso), 3 (Admitido) o 4 (Rechazado)',
            $validator->errors()->first('estado')
        );
    }

    #[Test]
    public function acepta_estado_valido(): void
    {
        $request = new UpdateAspiranteRequest();
        $rules = $request->rules();

        // Estado 1 = En proceso
        $validator = Validator::make([
            'estado' => 1,
        ], $rules, $request->messages());

        $this->assertFalse($validator->fails());

        // Estado 3 = Admitido
        $validator = Validator::make([
            'estado' => 3,
        ], $rules, $request->messages());

        $this->assertFalse($validator->fails());

        // Estado 4 = Rechazado
        $validator = Validator::make([
            'estado' => 4,
        ], $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function acepta_observaciones_opcionales(): void
    {
        $request = new UpdateAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'observaciones' => 'Observaciones de prueba',
        ], $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function valida_observaciones_maximo_500_caracteres(): void
    {
        $request = new UpdateAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'observaciones' => str_repeat('a', 501),
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('observaciones', $validator->errors()->toArray());
    }

    #[Test]
    public function acepta_solo_estado(): void
    {
        $request = new UpdateAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'estado' => 3,
        ], $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function acepta_solo_observaciones(): void
    {
        $request = new UpdateAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'observaciones' => 'Solo observaciones',
        ], $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function acepta_estado_y_observaciones(): void
    {
        $request = new UpdateAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'estado' => 3,
            'observaciones' => 'Observaciones con estado',
        ], $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function prepareForValidation_trim_observaciones(): void
    {
        $request = new UpdateAspiranteRequest();
        
        // Simular request con espacios
        $request->merge(['observaciones' => '  Observaciones con espacios  ']);
        
        // Usar reflection para llamar al método protegido
        // En PHP 8.1+ setAccessible() ya no es necesario
        $reflection = new \ReflectionClass($request);
        $method = $reflection->getMethod('prepareForValidation');
        $method->invoke($request);
        
        $this->assertEquals('Observaciones con espacios', $request->observaciones);
    }

    #[Test]
    public function prepareForValidation_acepta_observaciones_null(): void
    {
        $request = new UpdateAspiranteRequest();
        
        $request->merge(['observaciones' => null]);
        
        // En PHP 8.1+ setAccessible() ya no es necesario
        $reflection = new \ReflectionClass($request);
        $method = $reflection->getMethod('prepareForValidation');
        $method->invoke($request);
        
        $this->assertNull($request->observaciones);
    }

    #[Test]
    public function authorize_retorna_true(): void
    {
        $request = new UpdateAspiranteRequest();

        $this->assertTrue($request->authorize());
    }

    #[Test]
    public function mensajes_personalizados_estan_definidos(): void
    {
        $request = new UpdateAspiranteRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('estado.required', $messages);
        $this->assertArrayHasKey('estado.in', $messages);
        $this->assertArrayHasKey('observaciones.max', $messages);
    }
}

