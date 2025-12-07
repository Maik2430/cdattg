<?php

declare(strict_types=1);

namespace Tests\Complementarios\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\Complementarios\StoreAspiranteRequest;
use App\Models\Persona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\Complementarios\Concerns\SeedsComplementariosDatabase;

class StoreAspiranteRequestTest extends TestCase
{
    use RefreshDatabase;
    use SeedsComplementariosDatabase;

    private const NUMERO_DOCUMENTO_TEST = '1234567890';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedComplementariosDatabaseIfNeeded();
    }

    #[Test]
    public function valida_numero_documento_requerido(): void
    {
        $request = new StoreAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('numero_documento', $validator->errors()->toArray());
        $this->assertStringContainsString(
            'obligatorio',
            $validator->errors()->first('numero_documento')
        );
    }

    #[Test]
    public function valida_numero_documento_debe_existir(): void
    {
        $request = new StoreAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'numero_documento' => '9999999999',
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('numero_documento', $validator->errors()->toArray());
        $this->assertStringContainsString(
            'No se encontró ninguna persona',
            $validator->errors()->first('numero_documento')
        );
    }

    #[Test]
    public function valida_numero_documento_maximo_191_caracteres(): void
    {
        $request = new StoreAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'numero_documento' => str_repeat('1', 192),
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('numero_documento', $validator->errors()->toArray());
    }

    #[Test]
    public function acepta_numero_documento_valido(): void
    {
        $request = new StoreAspiranteRequest();
        $rules = $request->rules();

        Persona::factory()->create([
            'numero_documento' => self::NUMERO_DOCUMENTO_TEST,
        ]);

        $validator = Validator::make([
            'numero_documento' => self::NUMERO_DOCUMENTO_TEST,
        ], $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function acepta_observaciones_opcionales(): void
    {
        $request = new StoreAspiranteRequest();
        $rules = $request->rules();

        Persona::factory()->create([
            'numero_documento' => self::NUMERO_DOCUMENTO_TEST,
        ]);

        $validator = Validator::make([
            'numero_documento' => self::NUMERO_DOCUMENTO_TEST,
            'observaciones' => 'Observaciones de prueba',
        ], $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function valida_observaciones_maximo_500_caracteres(): void
    {
        $request = new StoreAspiranteRequest();
        $rules = $request->rules();

        Persona::factory()->create([
            'numero_documento' => self::NUMERO_DOCUMENTO_TEST,
        ]);

        $validator = Validator::make([
            'numero_documento' => self::NUMERO_DOCUMENTO_TEST,
            'observaciones' => str_repeat('a', 501),
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('observaciones', $validator->errors()->toArray());
    }

    #[Test]
    public function prepareForValidation_trim_numero_documento(): void
    {
        $request = new StoreAspiranteRequest();
        
        // Simular request con espacios
        $request->merge(['numero_documento' => '  ' . self::NUMERO_DOCUMENTO_TEST . '  ']);
        
        // Usar reflection para llamar al método protegido
        // En PHP 8.1+ setAccessible() ya no es necesario
        $reflection = new \ReflectionClass($request);
        $method = $reflection->getMethod('prepareForValidation');
        $method->invoke($request);
        
        $this->assertEquals(self::NUMERO_DOCUMENTO_TEST, $request->numero_documento);
    }

    #[Test]
    public function authorize_retorna_true(): void
    {
        $request = new StoreAspiranteRequest();

        $this->assertTrue($request->authorize());
    }

    #[Test]
    public function mensajes_personalizados_estan_definidos(): void
    {
        $request = new StoreAspiranteRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('numero_documento.required', $messages);
        $this->assertArrayHasKey('numero_documento.exists', $messages);
        $this->assertArrayHasKey('observaciones.max', $messages);
    }
}

