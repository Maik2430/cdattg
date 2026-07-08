<?php

namespace App\Livewire\Concerns\IngresoSalida;

use App\Exceptions\PersonaException;
use App\Models\Sede;
use App\Services\PersonaIngresoSalidaService;
use Illuminate\Support\Facades\Log;

trait HandlesIngresoSalidaRegistroActions
{
    use HandlesIngresoSalidaEstadoHelpers;
    use HandlesIngresoSalidaStateHelpers;

    public function registrarIngresoSalida(): void
    {
        $this->validate([
            'sedeId' => 'required|integer|exists:sedes,id',
            'personaId' => 'required|integer|exists:personas,id',
        ], [
            'sedeId.required' => 'Debe seleccionar una sede',
            'sedeId.exists' => 'La sede seleccionada no es válida',
            'personaId.required' => 'No hay una persona seleccionada',
            'personaId.exists' => 'La persona seleccionada no es válida',
        ]);

        $this->procesando = true;

        if ($this->mostrarModalSede) {
            $this->mostrarModalSede = false;
        }

        try {
            $service = app(PersonaIngresoSalidaService::class);

            if ($this->accionPendiente === 'entrada') {
                $this->registrarEntrada($service);
            } else {
                $this->registrarSalida($service);
            }

            $this->observaciones = '';
            $this->accionPendiente = null;

            if ($this->personaEncontrada) {
                $this->personaEncontrada->refresh();
            }
        } catch (PersonaException $e) {
            $this->mostrarMensaje('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error registrando ingreso/salida: '.$e->getMessage());
            $this->mostrarMensaje(
                'error',
                'Error al procesar la solicitud: '.$e->getMessage()
            );
        } finally {
            $this->procesando = false;
        }
    }

    private function registrarEntrada(PersonaIngresoSalidaService $service): void
    {
        if ($this->tieneEntradaActivaEnSede($this->sedeId)) {
            $mensaje = 'Ya existe un registro de entrada sin salida para hoy en esta sede.';
            $this->mostrarMensaje('warning', $mensaje);
            $this->procesando = false;

            return;
        }

        $service->registrarEntrada(
            $this->personaId,
            $this->sedeId,
            null,
            null,
            $this->observaciones ?: null
        );

        $this->mostrarMensaje('success', 'Entrada registrada correctamente');
        $this->dispatch('entrada-registrada', [
            'persona' => $this->personaEncontrada->primer_nombre.' '.
                $this->personaEncontrada->primer_apellido,
            'sede' => $this->sedeSeleccionada->sede ?? 'N/A',
        ]);
    }

    private function registrarSalida(PersonaIngresoSalidaService $service): void
    {
        $service->registrarSalida(
            $this->personaId,
            $this->sedeId,
            $this->observaciones ?: null
        );

        if (! $this->sedeSeleccionada && $this->sedeId) {
            $this->sedeSeleccionada = Sede::find($this->sedeId);
        }

        $nombrePersona = $this->personaEncontrada->primer_nombre.' '.
            $this->personaEncontrada->primer_apellido;
        $nombreSede = $this->sedeSeleccionada ? $this->sedeSeleccionada->sede : 'N/A';

        $this->mostrarMensaje('success', 'Salida registrada correctamente');
        $this->dispatch('salida-registrada', [
            'persona' => $nombrePersona,
            'sede' => $nombreSede,
        ]);
    }
}
