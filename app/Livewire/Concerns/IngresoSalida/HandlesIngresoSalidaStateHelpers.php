<?php

namespace App\Livewire\Concerns\IngresoSalida;

trait HandlesIngresoSalidaStateHelpers
{
    protected function resetearEstado(): void
    {
        $this->personaEncontrada = null;
        $this->mostrarFormulario = false;
        $this->modoEdicion = false;
        $this->personaId = null;
        $this->datosPersona = [];
        $this->mensaje = '';
        $this->tipoMensaje = '';
    }

    protected function mostrarMensaje(string $tipo, string $mensaje): void
    {
        $this->tipoMensaje = $tipo;
        $this->mensaje = $mensaje;

        $this->dispatch('mostrar-mensaje', [
            'tipo' => $tipo,
            'mensaje' => $mensaje,
        ]);
    }
}
