<?php

namespace App\Observers\Concerns\AspiranteComplementario;

use App\Models\Complementarios\ComplementarioOfertado;
use App\Repositories\Complementarios\ComplementarioOfertadoRepository;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

trait HandlesAspiranteComplementarioReplicationHelpers
{
    private function crearNuevoComplementario(ComplementarioOfertado $complementarioOriginal): ComplementarioOfertado
    {
        $nuevoCodigo = $this->generarCodigoUnico($complementarioOriginal->codigo);

        $nuevoComplementario = ComplementarioOfertado::create([
            'catalogo_id' => $complementarioOriginal->catalogo_id,
            'codigo' => $nuevoCodigo,
            'justificacion' => $complementarioOriginal->justificacion,
            'cupos' => $complementarioOriginal->cupos,
            'estado_id' => $this->obtenerEstadoIdLegacy(1),
            'jornada_id' => $complementarioOriginal->jornada_id,
            'ambiente_id' => $complementarioOriginal->ambiente_id,
            'user_create_id' => Auth::id() ?? 1,
            'user_edit_id' => Auth::id() ?? 1,
        ]);

        $this->copiarRelaciones($complementarioOriginal, $nuevoComplementario);

        return $nuevoComplementario;
    }

    private function copiarRelaciones(ComplementarioOfertado $original, ComplementarioOfertado $nuevo): void
    {
        $original->loadMissing([
            'diasFormacion',
            'competencias',
            'raps',
            'guiasAprendizaje',
        ]);

        if ($original->diasFormacion->isNotEmpty()) {
            $diasFormacionData = $original->diasFormacion->mapWithKeys(function ($dia) {
                return [
                    $dia->id => [
                        'hora_inicio' => $dia->pivot->hora_inicio,
                        'hora_fin' => $dia->pivot->hora_fin,
                    ],
                ];
            })->toArray();

            $nuevo->diasFormacion()->sync($diasFormacionData);
        }

        if ($original->competencias->isNotEmpty()) {
            $competenciasData = $original->competencias->mapWithKeys(function ($competencia) {
                return [
                    $competencia->id => [
                        'user_create_id' => $competencia->pivot->user_create_id ?? null,
                        'user_edit_id' => $competencia->pivot->user_edit_id ?? null,
                    ],
                ];
            })->toArray();

            $nuevo->competencias()->sync($competenciasData);
        }

        if ($original->raps->isNotEmpty()) {
            $rapsData = $original->raps->mapWithKeys(function ($rap) {
                return [
                    $rap->id => [
                        'user_create_id' => $rap->pivot->user_create_id ?? null,
                        'user_edit_id' => $rap->pivot->user_edit_id ?? null,
                    ],
                ];
            })->toArray();

            $nuevo->raps()->sync($rapsData);
        }

        if ($original->guiasAprendizaje->isNotEmpty()) {
            $guiasData = $original->guiasAprendizaje->mapWithKeys(function ($guia) {
                return [
                    $guia->id => [
                        'user_create_id' => $guia->pivot->user_create_id ?? null,
                        'user_edit_id' => $guia->pivot->user_edit_id ?? null,
                    ],
                ];
            })->toArray();

            $nuevo->guiasAprendizaje()->sync($guiasData);
        }
    }

    private function generarCodigoUnico(string $codigoOriginal): string
    {
        if (preg_match('/^(.+)-(\d+)$/', $codigoOriginal, $matches)) {
            $codigoBase = $matches[1];
            $ultimoNumero = (int) $matches[2];
        } else {
            $codigoBase = $codigoOriginal;
            $ultimoNumero = 1;
        }

        $siguienteNumero = $ultimoNumero + 1;
        $nuevoCodigo = "{$codigoBase}-{$siguienteNumero}";

        while (ComplementarioOfertado::where('codigo', $nuevoCodigo)->exists()) {
            $siguienteNumero++;
            $nuevoCodigo = "{$codigoBase}-{$siguienteNumero}";
        }

        return $nuevoCodigo;
    }

    private function obtenerEstadoIdLegacy(int $valorLegacy): int
    {
        /** @var ComplementarioOfertadoRepository $repository */
        $repository = $this->complementarioRepository;
        $estadoId = $repository->getEstadoIdByLegacyValue($valorLegacy);

        if (! $estadoId) {
            throw new RuntimeException(sprintf(
                'No se encontró el parámetro de estado para el valor legacy %d',
                $valorLegacy
            ));
        }

        return $estadoId;
    }
}
