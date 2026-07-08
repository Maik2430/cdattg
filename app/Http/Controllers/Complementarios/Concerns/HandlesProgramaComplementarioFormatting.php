<?php

namespace App\Http\Controllers\Complementarios\Concerns;

use App\Models\Complementarios\ComplementarioOfertado;

trait HandlesProgramaComplementarioFormatting
{
    /**
     * @return array<int, array<string, mixed>>
     */
    private function mapearDiasFormacion(ComplementarioOfertado $programa): array
    {
        return $programa->diasFormacion->map(static function ($dia): array {
            return [
                'dia_id' => (int) $dia->id,
                'hora_inicio' => $dia->pivot->hora_inicio ? substr($dia->pivot->hora_inicio, 0, 5) : null,
                'hora_fin' => $dia->pivot->hora_fin ? substr($dia->pivot->hora_fin, 0, 5) : null,
            ];
        })->toArray();
    }

    private function formatearDiasFormacion(ComplementarioOfertado $programa): string
    {
        return $programa->diasFormacion->map(static function ($dia): string {
            $nombreDia = $dia->parametro?->name ?? 'Día';

            return $nombreDia . ' (' . $dia->pivot->hora_inicio . ' - ' . $dia->pivot->hora_fin . ')';
        })->implode(', ');
    }

    /**
     * @return array<int, array{dia: string, hora_inicio: string|null, hora_fin: string|null}>
     */
    private function mapearDiasFormacionPublico(ComplementarioOfertado $programa): array
    {
        return $programa->diasFormacion
            ->map(static function ($dia): array {
                return [
                    'dia' => (string) ($dia->parametro?->name ?? 'Día'),
                    'hora_inicio' => $dia->pivot->hora_inicio ? substr((string) $dia->pivot->hora_inicio, 0, 5) : null,
                    'hora_fin' => $dia->pivot->hora_fin ? substr((string) $dia->pivot->hora_fin, 0, 5) : null,
                ];
            })
            ->values()
            ->all();
    }
}
