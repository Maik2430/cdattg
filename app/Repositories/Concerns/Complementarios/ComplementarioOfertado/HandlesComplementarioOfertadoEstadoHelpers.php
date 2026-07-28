<?php

namespace App\Repositories\Concerns\Complementarios\ComplementarioOfertado;

use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Tema;
use Exception;

trait HandlesComplementarioOfertadoEstadoHelpers
{
    private function getEstadoNombreByLegacyValue(int $estadoLegacy): string
    {
        return match ($estadoLegacy) {
            0 => 'Sin Oferta',
            1 => 'Con Oferta',
            2 => 'Cupos Llenos',
            default => 'Sin Oferta',
        };
    }

    public function getEstadoIdByLegacyValue(int $estadoLegacy): ?int
    {
        $nombreEstado = $this->getEstadoNombreByLegacyValue($estadoLegacy);

        try {
            $temaEstado = Tema::query()->find(1)
                ?? Tema::query()->firstOrCreate(
                    ['name' => 'ESTADOS'],
                    ['status' => 1]
                );

            $parametro = Parametro::query()->where('name', strtoupper($nombreEstado))->first()
                ?? Parametro::query()->where('name', $nombreEstado)->first()
                ?? Parametro::query()->create([
                    'name' => strtoupper($nombreEstado),
                    'status' => 1,
                ]);

            return ParametroTema::query()->firstOrCreate(
                [
                    'tema_id' => $temaEstado->id,
                    'parametro_id' => $parametro->id,
                ],
                ['status' => 1]
            )->id;
        } catch (Exception $e) {
            return null;
        }
    }
}
