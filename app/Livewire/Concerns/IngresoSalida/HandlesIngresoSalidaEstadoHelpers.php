<?php

namespace App\Livewire\Concerns\IngresoSalida;

use App\Models\PersonaIngresoSalida;

trait HandlesIngresoSalidaEstadoHelpers
{
    public function verificarEstadoPersona()
    {
        if (! $this->personaId) {
            return null;
        }

        return PersonaIngresoSalida::where('persona_id', $this->personaId)
            ->whereNull('timestamp_salida')
            ->whereDate('fecha_entrada', now()->toDateString())
            ->with('sede')
            ->first();
    }

    public function tieneEntradaActivaEnSede($sedeId): bool
    {
        if (! $this->personaId || ! $sedeId) {
            return false;
        }

        return PersonaIngresoSalida::where('persona_id', $this->personaId)
            ->where('sede_id', $sedeId)
            ->whereNull('timestamp_salida')
            ->whereDate('fecha_entrada', now()->toDateString())
            ->exists();
    }
}
