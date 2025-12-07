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
            $q->where('temas.id', 16); // CARACTERIZACION COMPLEMENTARIA
        })->first();
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
        $modalidad = \App\Models\ParametroTema::where('tema_id', 5)
            ->whereIn('parametro_id', [18, 19, 20])
            ->first();

        $jornada = \App\Models\JornadaFormacion::first();
        $ambiente = \App\Models\Ambiente::first();

        if (!$modalidad || !$jornada || !$ambiente) {
            $missingData = [];
            if (!$modalidad) {
                $missingData[] = 'modalidad';
            }
            if (!$jornada) {
                $missingData[] = 'jornada';
            }
            if (!$ambiente) {
                $missingData[] = 'ambiente';
            }
            throw new ReferenceDataNotAvailableException('', implode(', ', $missingData));
        }

        return ComplementarioOfertado::create([
            'codigo' => 'TEST-PROG-' . uniqid(),
            'nombre' => 'Programa de Prueba',
            'justificacion' => 'Justificación de prueba',
            'requisitos_ingreso' => 'Requisitos de prueba',
            'estado' => 1,
            'duracion' => 30,
            'cupos' => 50,
            'modalidad_id' => $modalidad->id,
            'jornada_id' => $jornada->id,
            'ambiente_id' => $ambiente->id,
        ]);
    }

    /**
     * Preparar temas y parámetros necesarios para formularios
     */
    protected function prepararTemasYParametros(): void
    {
        $temaTipoDoc = Tema::firstOrCreate(['id' => 2], ['name' => 'TIPO DE DOCUMENTO']);
        $temaGenero = Tema::firstOrCreate(['id' => 3], ['name' => 'GENERO']);
        Tema::firstOrCreate(['id' => 16], ['name' => 'CARACTERIZACION COMPLEMENTARIA']);
        $temaVia = Tema::firstOrCreate(['id' => 17], ['name' => 'VIA']);
        $temaLetra = Tema::firstOrCreate(['id' => 18], ['name' => 'LETRA']);

        $parametro1 = Parametro::firstOrCreate(['id' => 1], ['name' => 'CEDULA']);
        Parametro::firstOrCreate(['id' => 2], ['name' => 'TARJETA IDENTIDAD']);
        $parametro3 = Parametro::firstOrCreate(['id' => 3], ['name' => 'MASCULINO']);
        $parametroVia = Parametro::firstOrCreate(['id' => 100], ['name' => 'CALLE']);
        $parametroLetra = Parametro::firstOrCreate(['id' => 101], ['name' => 'A']);
        $parametroCardinal = Parametro::firstOrCreate(['id' => 102], ['name' => 'NORTE']);

        if (!$temaTipoDoc->parametros()->where('parametros.id', $parametro1->id)->exists()) {
            $temaTipoDoc->parametros()->attach($parametro1->id, ['status' => 1]);
        }
        if (!$temaGenero->parametros()->where('parametros.id', $parametro3->id)->exists()) {
            $temaGenero->parametros()->attach($parametro3->id, ['status' => 1]);
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
        $caracterizacion = $this->obtenerCaracterizacion();

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
            'caracterizaciones' => $caracterizacion ? [$caracterizacion->id] : [],
            'observaciones' => 'Aspirante creado desde pruebas',
        ];
    }
}

