<?php

declare(strict_types=1);

namespace Tests\Complementarios\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\Complementarios\CreateAspiranteRequest;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Pais;
use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Tema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\Complementarios\Concerns\SeedsComplementariosDatabase;

class CreateAspiranteRequestTest extends TestCase
{
    use RefreshDatabase;
    use SeedsComplementariosDatabase;

    private const NUMERO_DOCUMENTO_TEST = '1234567890';
    private const NUMERO_DOCUMENTO_NUEVO = '9876543210';
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

    private function obtenerTipoDocumento(): ?Parametro
    {
        return Parametro::whereHas('temas', function ($q) {
            $q->where('temas.id', 2);
        })->first();
    }

    /**
     * Asegura Tema (id 2 / TIPO DE DOCUMENTO), Parametro y ParametroTema.
     */
    private function ensureTipoDocumento(): Parametro
    {
        $tipoDocumento = $this->obtenerTipoDocumento();
        if ($tipoDocumento) {
            return $tipoDocumento;
        }

        $tema = Tema::query()->find(2);
        if (! $tema) {
            $tema = new Tema();
            $tema->forceFill([
                'id' => 2,
                'name' => 'TIPO DE DOCUMENTO',
                'status' => 1,
            ]);
            $tema->save();
        }

        $parametro = Parametro::firstOrCreate(
            ['name' => 'CEDULA DE CIUDADANIA'],
            ['status' => 1]
        );

        ParametroTema::firstOrCreate(
            [
                'tema_id' => 2,
                'parametro_id' => $parametro->id,
            ],
            ['status' => 1]
        );

        return $parametro->fresh() ?? $parametro;
    }

    /**
     * @return array{pais: Pais, departamento: Departamento, municipio: Municipio}
     */
    private function ensureUbicacion(): array
    {
        $pais = Pais::first();
        if (! $pais) {
            $pais = Pais::create(['pais' => 'COLOMBIA', 'status' => 1]);
        }

        $departamento = Departamento::where('pais_id', $pais->id)->first();
        if (! $departamento) {
            $departamento = Departamento::factory()->create([
                'pais_id' => $pais->id,
                'departamento' => 'CUNDINAMARCA',
                'status' => 1,
            ]);
        }

        $municipio = Municipio::where('departamento_id', $departamento->id)->first();
        if (! $municipio) {
            $municipio = Municipio::factory()->create([
                'departamento_id' => $departamento->id,
                'municipio' => 'BOGOTA',
                'status' => 1,
            ]);
        }

        return [
            'pais' => $pais,
            'departamento' => $departamento,
            'municipio' => $municipio,
        ];
    }

    #[Test]
    public function valida_numero_documento_requerido(): void
    {
        $request = new CreateAspiranteRequest();
        $rules = $request->rules();

        $tipoDocumento = $this->ensureTipoDocumento();

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

        $tipoDocumento = $this->ensureTipoDocumento();

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

        $tipoDocumento = $this->ensureTipoDocumento();

        $validator = Validator::make([
            'tipo_documento' => $tipoDocumento->id,
            'numero_documento' => self::NUMERO_DOCUMENTO_TEST,
            'primer_nombre' => self::PRIMER_NOMBRE_TEST,
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('primer_apellido', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_documento_identidad_requerido(): void
    {
        $request = new CreateAspiranteRequest();
        $rules = $request->rules();

        $tipoDocumento = $this->ensureTipoDocumento();

        $validator = Validator::make([
            'tipo_documento' => $tipoDocumento->id,
            'numero_documento' => self::NUMERO_DOCUMENTO_TEST,
            'primer_nombre' => self::PRIMER_NOMBRE_TEST,
            'primer_apellido' => self::PRIMER_APELLIDO_TEST,
        ], $rules, $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('documento_identidad', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_unicidad_numero_documento(): void
    {
        $request = new CreateAspiranteRequest();
        $rules = $request->rules();

        $tipoDocumento = $this->ensureTipoDocumento();

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

        $tipoDocumento = $this->ensureTipoDocumento();

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

        $tipoDocumento = $this->ensureTipoDocumento();
        $ubicacion = $this->ensureUbicacion();

        $validator = Validator::make([
            'tipo_documento' => $tipoDocumento->id,
            'numero_documento' => self::NUMERO_DOCUMENTO_NUEVO,
            'primer_nombre' => self::PRIMER_NOMBRE_TEST,
            'primer_apellido' => self::PRIMER_APELLIDO_TEST,
            'email' => 'juan@example.com',
            'pais_id' => $ubicacion['pais']->id,
            'departamento_id' => $ubicacion['departamento']->id,
            'municipio_id' => $ubicacion['municipio']->id,
            'documento_identidad' => UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf'),
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
