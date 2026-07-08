<?php

namespace App\Livewire\Concerns\IngresoSalida;

use App\Services\PersonaService;
use Illuminate\Support\Facades\Log;

trait HandlesIngresoSalidaSearchActions
{
    use HandlesIngresoSalidaStateHelpers;

    public function updatedNumeroDocumento(): void
    {
        $this->buscarPersona();
    }

    public function buscarPersona(): void
    {
        $this->resetearEstado();

        if (strlen(trim($this->numeroDocumento)) < 3) {
            return;
        }

        try {
            $persona = app(PersonaService::class)
                ->buscarPorDocumento(trim($this->numeroDocumento));

            if ($persona) {
                $this->personaEncontrada = $persona;
                $this->personaId = $persona->id;
                $this->cargarDatosPersona($persona);
                $this->mostrarFormulario = true;
                $this->modoEdicion = false;
            } else {
                $this->mostrarFormulario = false;
                $this->personaEncontrada = null;
            }
        } catch (\Exception $e) {
            Log::error('Error buscando persona: '.$e->getMessage());
            $this->mostrarMensaje('error', 'Error al buscar persona: '.$e->getMessage());
        }
    }

    protected function cargarDatosPersona($persona): void
    {
        $this->datosPersona = [
            'id' => $persona->id,
            'tipo_documento' => $persona->tipo_documento,
            'numero_documento' => $persona->numero_documento,
            'primer_nombre' => $persona->primer_nombre,
            'segundo_nombre' => $persona->segundo_nombre ?? '',
            'primer_apellido' => $persona->primer_apellido,
            'segundo_apellido' => $persona->segundo_apellido ?? '',
            'email' => $persona->email ?? '',
            'celular' => $persona->celular ?? '',
            'telefono' => $persona->telefono ?? '',
        ];
    }

    public function limpiarBusqueda(): void
    {
        $this->resetearEstado();
        $this->numeroDocumento = '';
    }
}
