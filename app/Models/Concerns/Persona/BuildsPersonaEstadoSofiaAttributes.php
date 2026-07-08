<?php

namespace App\Models\Concerns\Persona;

use App\Models\ParametroTema;
use App\Models\Tema;

trait BuildsPersonaEstadoSofiaAttributes
{
    /**
     * Accesor para obtener la etiqueta del estado de SenaSofiaPlus.
     */
    public function getEstadoSofiaLabelAttribute(): string
    {
        $label = 'Desconocido';

        if ($this->estado_sofia &&
            ($parametro = $this->estadoSofiaParametro) &&
            ($temaEstados = Tema::where('name', 'ESTADOS SOFIA')->first()) &&
            ParametroTema::where('tema_id', $temaEstados->id)
                ->where('parametro_id', $parametro->id)
                ->where('status', 1)
                ->exists()) {
            $label = $parametro->name;
        }

        return $label;
    }

    /**
     * Accesor para obtener la clase CSS del badge del estado de SenaSofiaPlus.
     */
    public function getEstadoSofiaBadgeClassAttribute(): string
    {
        $badgeClass = 'bg-dark';

        if ($this->estado_sofia &&
            ($parametro = $this->estadoSofiaParametro) &&
            ($temaEstados = Tema::where('name', 'ESTADOS SOFIA')->first()) &&
            ParametroTema::where('tema_id', $temaEstados->id)
                ->where('parametro_id', $parametro->id)
                ->where('status', 1)
                ->exists()) {
            $badgeClass = match (strtoupper($parametro->name)) {
                'NO REGISTRADO' => 'bg-danger',
                'REGISTRADO' => 'bg-success',
                'REQUIERE CAMBIO' => 'bg-warning',
                default => 'bg-dark',
            };
        }

        return $badgeClass;
    }
}
