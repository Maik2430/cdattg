<?php

declare(strict_types=1);

namespace Tests\Complementarios\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\Complementarios\RechazarAspiranteRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;

class RechazarAspiranteRequestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function acepta_motivo_rechazo_opcional(): void
    {
        $request = new RechazarAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function acepta_motivo_rechazo_valido(): void
    {
        $request = new RechazarAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'motivo_rechazo' => 'No cumple requisitos de edad',
        ], $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function valida_motivo_rechazo_maximo_500_caracteres(): void
    {
        $request = new RechazarAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'motivo_rechazo' => str_repeat('a', 501),
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('motivo_rechazo', $validator->errors()->toArray());
    }

    #[Test]
    public function acepta_observaciones_opcionales(): void
    {
        $request = new RechazarAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'observaciones' => 'Observaciones de rechazo',
        ], $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function valida_observaciones_maximo_500_caracteres(): void
    {
        $request = new RechazarAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'observaciones' => str_repeat('a', 501),
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('observaciones', $validator->errors()->toArray());
    }

    #[Test]
    public function acepta_motivo_rechazo_y_observaciones(): void
    {
        $request = new RechazarAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'motivo_rechazo' => 'No cumple requisitos',
            'observaciones' => 'Observaciones adicionales',
        ], $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function prepareForValidation_trim_motivo_rechazo(): void
    {
        $request = new RechazarAspiranteRequest();
        
        $request->merge(['motivo_rechazo' => '  Motivo con espacios  ']);
        
        // Usar reflection para llamar al método protegido
        // En PHP 8.1+ setAccessible() ya no es necesario
        $reflection = new \ReflectionClass($request);
        $method = $reflection->getMethod('prepareForValidation');
        $method->invoke($request);
        
        $this->assertEquals('Motivo con espacios', $request->motivo_rechazo);
    }

    #[Test]
    public function prepareForValidation_trim_observaciones(): void
    {
        $request = new RechazarAspiranteRequest();
        
        $request->merge(['observaciones' => '  Observaciones con espacios  ']);
        
        // En PHP 8.1+ setAccessible() ya no es necesario
        $reflection = new \ReflectionClass($request);
        $method = $reflection->getMethod('prepareForValidation');
        $method->invoke($request);
        
        $this->assertEquals('Observaciones con espacios', $request->observaciones);
    }

    #[Test]
    public function prepareForValidation_acepta_motivo_rechazo_null(): void
    {
        $request = new RechazarAspiranteRequest();
        
        $request->merge(['motivo_rechazo' => null]);
        
        // En PHP 8.1+ setAccessible() ya no es necesario
        $reflection = new \ReflectionClass($request);
        $method = $reflection->getMethod('prepareForValidation');
        $method->invoke($request);
        
        $this->assertNull($request->motivo_rechazo);
    }

    #[Test]
    public function authorize_retorna_true(): void
    {
        $request = new RechazarAspiranteRequest();

        $this->assertTrue($request->authorize());
    }

    #[Test]
    public function mensajes_personalizados_estan_definidos(): void
    {
        $request = new RechazarAspiranteRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('motivo_rechazo.max', $messages);
        $this->assertArrayHasKey('observaciones.max', $messages);
    }
}

