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
            $temaEstado = Tema::find(1);

            if ($temaEstado) {
                $parametro = Parametro::where('name', strtoupper($nombreEstado))->first();

                if (! $parametro) {
                    $parametro = Parametro::where('name', $nombreEstado)->first();
                }

                if ($parametro) {
                    $parametroTema = ParametroTema::where('tema_id', $temaEstado->id)
                        ->where('parametro_id', $parametro->id)
                        ->first();

                    if ($parametroTema) {
                        return $parametroTema->id;
                    }
                }
            }
        } catch (Exception $e) {
            // Si hay error, retornar null
        }

        return null;
    }
}
