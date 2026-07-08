<?php

namespace App\Livewire\Concerns\IngresoSalida;

use App\Models\PersonaIngresoSalida;
use App\Models\Sede;

trait HandlesIngresoSalidaModalActions
{
    use HandlesIngresoSalidaEstadoHelpers;
    use HandlesIngresoSalidaStateHelpers;

    public function abrirModalEntrada(): void
    {
        if (! $this->personaEncontrada) {
            $this->mostrarMensaje('warning', 'Debe buscar una persona primero');

            return;
        }

        $entradaActiva = $this->verificarEstadoPersona();
        if ($entradaActiva) {
            $sedeNombre = $entradaActiva->sede
                ? $entradaActiva->sede->sede
                : 'una sede';
            $mensaje = "La persona ya tiene un registro de entrada activo en {$sedeNombre}. ".
                'Debe registrar la salida primero.';
            $this->mostrarMensaje('warning', $mensaje);

            return;
        }

        $this->accionPendiente = 'entrada';
        $this->mostrarModalSede = true;
    }

    public function abrirModalSalida(): void
    {
        if (! $this->personaEncontrada) {
            $this->mostrarMensaje('warning', 'Debe buscar una persona primero');

            return;
        }

        $entradaActiva = PersonaIngresoSalida::where('persona_id', $this->personaId)
            ->whereNull('timestamp_salida')
            ->whereDate('fecha_entrada', now()->toDateString())
            ->first();

        if (! $entradaActiva) {
            $this->mostrarMensaje('warning', 'La persona no tiene un registro de entrada activo para hoy.');

            return;
        }

        $this->sedeId = $entradaActiva->sede_id;
        $this->sedeSeleccionada = $entradaActiva->sede;
        $this->accionPendiente = 'salida';

        $this->registrarIngresoSalida();
    }

    public function cerrarModalSede(): void
    {
        $this->mostrarModalSede = false;
        $this->sedeId = null;
        $this->accionPendiente = null;
    }

    public function updatedSedeId(): void
    {
        if ($this->sedeId) {
            $this->sedeSeleccionada = Sede::find($this->sedeId);
        } else {
            $this->sedeSeleccionada = null;
        }
    }
}
