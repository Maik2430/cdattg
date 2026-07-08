<?php

namespace App\Livewire\Concerns\IngresoSalida;

use App\Models\Sede;

trait HandlesIngresoSalidaRenderActions
{
    use HandlesIngresoSalidaEstadoHelpers;

    public function render()
    {
        $sedes = Sede::where('status', 1)->orderBy('sede')->get();
        $entradaActiva = $this->verificarEstadoPersona();
        $estaDentro = $entradaActiva !== null;

        return view('livewire.ingreso-salida-component', [
            'sedes' => $sedes,
            'estaDentro' => $estaDentro,
            'entradaActiva' => $entradaActiva,
        ]);
    }
}
