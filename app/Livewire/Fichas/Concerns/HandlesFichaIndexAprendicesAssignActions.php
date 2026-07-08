<?php

namespace App\Livewire\Fichas\Concerns;

trait HandlesFichaIndexAprendicesAssignActions
{
    public function asignarAprendices()
    {
        // Guardar el conteo antes de limpiar la selección
        $conteoPersonas = count($this->selectedPersonas);

        // Depuración antes de asignar
        \Log::info('=== INICIO ASIGNACIÓN APRENDICES ===');
        \Log::info('Datos antes de asignar:', [
            'selectedPersonas' => $this->selectedPersonas,
            'count' => $conteoPersonas,
            'ficha_id' => $this->selectedFicha?->id,
            'ficha_codigo' => $this->selectedFicha?->ficha,
        ]);

        if (empty($this->selectedPersonas)) {
            \Log::info('No hay personas seleccionadas - abortando');
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Seleccione al menos una persona para asignar']);

            return;
        }

        try {
            \DB::beginTransaction();

            \Log::info('Iniciando transacción DB para asignar aprendices');

            foreach ($this->selectedPersonas as $index => $personaId) {
                \Log::info('Asignando persona '.($index + 1).':', ['persona_id' => $personaId]);

                \App\Models\Aprendiz::create([
                    'persona_id' => $personaId,
                    'ficha_caracterizacion_id' => $this->selectedFicha->id,
                    'estado' => 1,
                    'user_create_id' => auth()->id(),
                    'user_edit_id' => auth()->id(),
                ]);
            }

            \DB::commit();

            \Log::info('Transacción confirmada - '.$conteoPersonas.' aprendices asignados');

            // Recargar la ficha para actualizar los aprendices asignados
            \Log::info('Recargando ficha con aprendices...');
            $this->selectedFicha = \App\Models\FichaCaracterizacion::with(['aprendices.persona'])->find($this->selectedFicha->id);

            \Log::info('Ficha recargada:', [
                'aprendices_count' => $this->selectedFicha?->aprendices->count(),
                'aprendices_ids' => $this->selectedFicha?->aprendices->pluck('id')->toArray(),
            ]);

            // Recargar personas disponibles (excluyendo las ya asignadas)
            $this->loadPersonasDisponibles();

            // Limpiar selección
            $this->reset(['selectedPersonas', 'selectAllPersonas']);

            // Forzar refresh de la vista
            $this->dispatch('refreshComponent');
            $this->dispatch('$refresh');

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $conteoPersonas.' personas asignadas como aprendices exitosamente',
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al asignar aprendices:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'selectedPersonas' => $this->selectedPersonas,
                'ficha_id' => $this->selectedFicha?->id,
            ]);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error al asignar aprendices: '.$e->getMessage()]);
        }

        \Log::info('=== FIN ASIGNACIÓN APRENDICES ===');
    }
}
