<?php

namespace App\Livewire\Fichas\Concerns;

trait HandlesFichaIndexAprendicesDesassignActions
{
    public function desasignarAprendices()
    {
        // Guardar el conteo antes de limpiar la selección
        $conteoAprendices = count($this->selectedAprendicesAsignados);

        if (empty($this->selectedAprendicesAsignados)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Seleccione al menos un aprendiz para desasignar',
            ]);

            return;
        }

        try {
            \DB::beginTransaction();

            \Log::info('=== INICIO DESASIGNACIÓN APRENDICES ===');
            \Log::info('Aprendices a desasignar:', [
                'selectedAprendicesAsignados' => $this->selectedAprendicesAsignados,
                'count' => $conteoAprendices,
                'ficha_id' => $this->selectedFicha?->id,
            ]);

            foreach ($this->selectedAprendicesAsignados as $aprendizId) {
                \Log::info('Desasignando aprendiz:', ['aprendiz_id' => $aprendizId]);

                // Eliminar el aprendiz (soft delete)
                $aprendiz = \App\Models\Aprendiz::find($aprendizId);
                if ($aprendiz) {
                    $aprendiz->delete();
                }
            }

            \DB::commit();

            \Log::info('Transacción confirmada - '.$conteoAprendices.' aprendices desasignados');

            // Recargar la ficha para actualizar los aprendices asignados
            $this->selectedFicha = \App\Models\FichaCaracterizacion::with(['aprendices.persona'])->find($this->selectedFicha->id);

            // Recargar personas disponibles (ahora disponibles los desasignados)
            $this->loadPersonasDisponibles();

            // Limpiar selección
            $this->reset(['selectedAprendicesAsignados', 'selectAllAprendicesAsignados']);

            // Forzar refresh de la vista
            $this->dispatch('refreshComponent');
            $this->dispatch('$refresh');

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $conteoAprendices.' aprendices desasignados exitosamente',
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al desasignar aprendices:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'selectedAprendicesAsignados' => $this->selectedAprendicesAsignados,
                'ficha_id' => $this->selectedFicha?->id,
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al desasignar aprendices: '.$e->getMessage(),
            ]);
        }

        \Log::info('=== FIN DESASIGNACIÓN APRENDICES ===');
    }
}
