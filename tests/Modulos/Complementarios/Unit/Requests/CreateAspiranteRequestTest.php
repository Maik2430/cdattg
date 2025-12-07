<?php

declare(strict_types=1);

namespace Tests\Complementarios\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\Complementarios\CreateAspiranteRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\Complementarios\Concerns\SeedsComplementariosDatabase;

class CreateAspiranteRequestTest extends TestCase
{
    use RefreshDatabase;
    use SeedsComplementariosDatabase;

    private const NUMERO_DOCUMENTO_TEST = '1234567890';
    private const NUMERO_DOCUMENTO_NUEVO = '9876543210';
    private const MENSAJE_DATOS_NO_DISPONIBLES = 'Datos de referencia no disponibles. Verificar seeders.';
    private const PRIMER_NOMBRE_TEST = 'Juan';
    private const PRIMER_APELLIDO_TEST = 'Pérez';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedComplementariosDatabaseIfNeeded();
    }

    #[Test]
    public function valida_tipo_documento_requerido(): void
    {
        $request = new CreateAspiranteRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'numero_documento' => '1234567890',
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tipo_documento', $validator->errors()->toArray());
    }

    private function obtenerTipoDocumento(): ?\App\Models\Parametro
    {
        return \App\Models\Parametro::whereHas('temas', function ($q) {
            $q->where('temas.id', 2);
        })->first();
    }

    #[Test]
    public function valida_numero_documento_requerido(): void
    {
        $request = new CreateAspiranteRequest();
        $rules = $request->rules();

        $tipoDocumento = $this->obtenerTipoDocumento();

        if (!$tipoDocumento) {
            $this->markTestSkipped(self::MENSAJE_DATOS_NO_DISPONIBLES);
        }

        $validator = Validator::make([
            'tipo_documento' => $tipoDocumento->id,
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('numero_documento', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_primer_nombre_requerido(): void
    {
        $request = new CreateAspiranteRequest();
        $rules = $request->rules();

        $tipoDocumento = $this->obtenerTipoDocumento();

        if (!$tipoDocumento) {
            $this->markTestSkipped(self::MENSAJE_DATOS_NO_DISPONIBLES);
        }

        $validator = Validator::make([
            'tipo_documento' => $tipoDocumento->id,
            'numero_documento' => self::NUMERO_DOCUMENTO_TEST,
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('primer_nombre', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_primer_apellido_requerido(): void
    {
        $request = new CreateAspiranteRequest();
        $rules = $request->rules();

        $tipoDocumento = $this->obtenerTipoDocumento();

        if (!$tipoDocumento) {
            $this->markTestSkipped(self::MENSAJE_DATOS_NO_DISPONIBLES);
        }

        $validator = Validator::make([
            'tipo_documento' => $tipoDocumento->id,
            'numero_documento' => self::NUMERO_DOCUMENTO_TEST,
            'primer_nombre' => self::PRIMER_NOMBRE_TEST,
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('primer_apellido', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_unicidad_numero_documento(): void
    {
        $request = new CreateAspiranteRequest();
        $rules = $request->rules();

        $tipoDocumento = $this->obtenerTipoDocumento();

        if (!$tipoDocumento) {
            $this->markTestSkipped(self::MENSAJE_DATOS_NO_DISPONIBLES);
        }

        // Crear persona existente
        \App\Models\Persona::factory()->create([
            'numero_documento' => self::NUMERO_DOCUMENTO_TEST,
        ]);

        $validator = Validator::make([
            'tipo_documento' => $tipoDocumento->id,
            'numero_documento' => self::NUMERO_DOCUMENTO_TEST,
            'primer_nombre' => self::PRIMER_NOMBRE_TEST,
            'primer_apellido' => self::PRIMER_APELLIDO_TEST,
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('numero_documento', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_formato_email(): void
    {
        $request = new CreateAspiranteRequest();
        $rules = $request->rules();

        $tipoDocumento = $this->obtenerTipoDocumento();

        if (!$tipoDocumento) {
            $this->markTestSkipped(self::MENSAJE_DATOS_NO_DISPONIBLES);
        }

        $validator = Validator::make([
            'tipo_documento' => $tipoDocumento->id,
            'numero_documento' => self::NUMERO_DOCUMENTO_TEST,
            'primer_nombre' => self::PRIMER_NOMBRE_TEST,
            'primer_apellido' => self::PRIMER_APELLIDO_TEST,
            'email' => 'email-invalido',
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function acepta_datos_validos(): void
    {
        $request = new CreateAspiranteRequest();
        $rules = $request->rules();

        $tipoDocumento = $this->obtenerTipoDocumento();

        $pais = \App\Models\Pais::first();
        $departamento = $pais ? \App\Models\Departamento::where('pais_id', $pais->id)->first() : null;
        $municipio = $departamento ? \App\Models\Municipio::where('departamento_id', $departamento->id)->first() : null;

        if (!$tipoDocumento || !$pais || !$departamento || !$municipio) {
            $this->markTestSkipped(self::MENSAJE_DATOS_NO_DISPONIBLES);
        }

        $validator = Validator::make([
            'tipo_documento' => $tipoDocumento->id,
            'numero_documento' => self::NUMERO_DOCUMENTO_NUEVO,
            'primer_nombre' => self::PRIMER_NOMBRE_TEST,
            'primer_apellido' => self::PRIMER_APELLIDO_TEST,
            'email' => 'juan@example.com',
            'pais_id' => $pais->id,
            'departamento_id' => $departamento->id,
            'municipio_id' => $municipio->id,
        ], $rules, $request->messages());

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function authorize_retorna_true(): void
    {
        $request = new CreateAspiranteRequest();

        $this->assertTrue($request->authorize());
    }

    #[Test]
    public function mensajes_personalizados_estan_definidos(): void
    {
        $request = new CreateAspiranteRequest();
        $messages = $request->messages();

        $this->assertIsArray($messages);
        $this->assertNotEmpty($messages);
    }
}

