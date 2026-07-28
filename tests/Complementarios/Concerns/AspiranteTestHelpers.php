<?php

declare(strict_types=1);

namespace Tests\Complementarios\Concerns;

use App\Models\Complementarios\ComplementarioOfertado;
use App\Models\Complementarios\AspiranteComplementario;
use App\Models\Persona;
use App\Models\Parametro;
use App\Models\Pais;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Tema;
use App\Exceptions\Complementarios\ReferenceDataNotAvailableException;

/**
 * Trait con métodos helper para tests de aspirantes complementarios.
 * Evita duplicación de código según buenas prácticas de SonarQube.
 */
trait AspiranteTestHelpers
{
    /**
     * Obtener tipo de documento para tests
     */
    protected function obtenerTipoDocumento(): ?Parametro
    {
        return Parametro::whereHas('temas', function ($q) {
            $q->where('temas.id', 2); // TIPO DE DOCUMENTO
        })->first();
    }

    /**
     * Obtener género para tests
     */
    protected function obtenerGenero(): ?Parametro
    {
        return Parametro::whereHas('temas', function ($q) {
            $q->where('temas.id', 3); // GENERO
        })->first();
    }

    /**
     * Obtener caracterización para tests
     */
    protected function obtenerCaracterizacion(): ?Parametro
    {
        return Parametro::whereHas('temas', function ($q) {
            $q->where('temas.id', 16); // CARACTERIZACION COMPLEMENTARIA / PERSONA CARACTERIZACION
        })->first();
    }

    /**
     * Asegura Tema 16 + Parametro + ParametroTema de caracterización.
     */
    protected function ensureCaracterizacion(): Parametro
    {
        $caracterizacion = $this->obtenerCaracterizacion();
        if ($caracterizacion) {
            return $caracterizacion;
        }

        $tema = Tema::query()->find(16);
        if (! $tema) {
            $tema = new Tema();
            $tema->forceFill([
                'id' => 16,
                'name' => 'PERSONA CARACTERIZACION',
                'status' => 1,
            ]);
            $tema->save();
        }

        $parametro = Parametro::firstOrCreate(
            ['name' => 'NINGUNA'],
            ['status' => 1]
        );

        \App\Models\ParametroTema::firstOrCreate(
            [
                'tema_id' => 16,
                'parametro_id' => $parametro->id,
            ],
            ['status' => 1]
        );

        return $parametro->fresh() ?? $parametro;
    }

    /**
     * Obtener datos de ubicación (país, departamento, municipio) para tests
     */
    protected function obtenerDatosUbicacion(): array
    {
        $pais = Pais::first();
        $departamento = $pais ? Departamento::where('pais_id', $pais->id)->first() : null;
        $municipio = $departamento ? Municipio::where('departamento_id', $departamento->id)->first() : null;

        return [
            'pais' => $pais,
            'departamento' => $departamento,
            'municipio' => $municipio,
        ];
    }

    /**
     * Crear programa complementario con datos mínimos requeridos
     */
    protected function crearProgramaComplementario(): ComplementarioOfertado
    {
        return ComplementarioOfertado::factory()->create();
    }

    /**
     * Preparar temas y parámetros necesarios para formularios
     */
    protected function prepararTemasYParametros(): void
    {
        $temaTipoDoc = Tema::firstOrCreate(['id' => 2], ['name' => 'TIPO DE DOCUMENTO']);
        $temaGenero = Tema::firstOrCreate(['id' => 3], ['name' => 'GENERO']);
        $temaCaracterizacion = Tema::firstOrCreate(['id' => 16], ['name' => 'PERSONA CARACTERIZACION']);
        $temaVia = Tema::firstOrCreate(['id' => 17], ['name' => 'VIA']);
        $temaLetra = Tema::firstOrCreate(['id' => 18], ['name' => 'LETRA']);

        $parametro1 = Parametro::firstOrCreate(['id' => 1], ['name' => 'CEDULA']);
        Parametro::firstOrCreate(['id' => 2], ['name' => 'TARJETA IDENTIDAD']);
        $parametro3 = Parametro::firstOrCreate(['id' => 3], ['name' => 'MASCULINO']);
        $parametroCaracterizacion = Parametro::firstOrCreate(['id' => 235], ['name' => 'NINGUNA']);
        $parametroVia = Parametro::firstOrCreate(['id' => 100], ['name' => 'CALLE']);
        $parametroLetra = Parametro::firstOrCreate(['id' => 101], ['name' => 'A']);
        $parametroCardinal = Parametro::firstOrCreate(['id' => 102], ['name' => 'NORTE']);

        if (!$temaTipoDoc->parametros()->where('parametros.id', $parametro1->id)->exists()) {
            $temaTipoDoc->parametros()->attach($parametro1->id, ['status' => 1]);
        }
        if (!$temaGenero->parametros()->where('parametros.id', $parametro3->id)->exists()) {
            $temaGenero->parametros()->attach($parametro3->id, ['status' => 1]);
        }
        if (!$temaCaracterizacion->parametros()->where('parametros.id', $parametroCaracterizacion->id)->exists()) {
            $temaCaracterizacion->parametros()->attach($parametroCaracterizacion->id, ['status' => 1]);
        }
        if (!$temaVia->parametros()->where('parametros.id', $parametroVia->id)->exists()) {
            $temaVia->parametros()->attach($parametroVia->id, ['status' => 1]);
        }
        if (!$temaLetra->parametros()->where('parametros.id', $parametroLetra->id)->exists()) {
            $temaLetra->parametros()->attach($parametroLetra->id, ['status' => 1]);
        }
        if (!$temaLetra->parametros()->where('parametros.id', $parametroCardinal->id)->exists()) {
            $temaLetra->parametros()->attach($parametroCardinal->id, ['status' => 1]);
        }

        $temaTipoDoc->load('parametros');
        $temaGenero->load('parametros');
        $temaCaracterizacion->load('parametros');
        $temaVia->load('parametros');
        $temaLetra->load('parametros');
    }

    /**
     * Crear datos completos de persona para tests
     */
    protected function crearDatosPersonaCompleta(string $numeroDocumento): array
    {
        $tipoDocumento = $this->obtenerTipoDocumento();
        $genero = $this->obtenerGenero();
        $ubicacion = $this->obtenerDatosUbicacion();
        $caracterizacion = $this->ensureCaracterizacion();

        if (!$tipoDocumento || !$genero || !$ubicacion['pais'] || !$ubicacion['departamento'] || !$ubicacion['municipio']) {
            $missingData = [];
            if (!$tipoDocumento) {
                $missingData[] = 'tipo_documento';
            }
            if (!$genero) {
                $missingData[] = 'genero';
            }
            if (!$ubicacion['pais']) {
                $missingData[] = 'pais';
            }
            if (!$ubicacion['departamento']) {
                $missingData[] = 'departamento';
            }
            if (!$ubicacion['municipio']) {
                $missingData[] = 'municipio';
            }
            throw new ReferenceDataNotAvailableException('', implode(', ', $missingData));
        }

        return [
            'tipo_documento' => $tipoDocumento->id,
            'numero_documento' => $numeroDocumento,
            'primer_nombre' => 'María',
            'segundo_nombre' => 'José',
            'primer_apellido' => 'González',
            'segundo_apellido' => 'López',
            'fecha_nacimiento' => '1995-05-15',
            'genero_id' => $genero->id,
            'telefono' => '6012345678',
            'celular' => '3001234567',
            'email' => 'maria@example.com',
            'pais_id' => $ubicacion['pais']->id,
            'departamento_id' => $ubicacion['departamento']->id,
            'municipio_id' => $ubicacion['municipio']->id,
            'direccion' => 'Calle 123 #45-67',
            'caracterizaciones' => [$caracterizacion->id],
            'observaciones' => 'Aspirante creado desde pruebas',
            'documento_identidad' => \Illuminate\Http\UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf'),
        ];
    }
}

