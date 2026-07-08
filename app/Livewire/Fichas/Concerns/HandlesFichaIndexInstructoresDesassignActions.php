<?php

namespace App\Livewire\Fichas\Concerns;

trait HandlesFichaIndexInstructoresDesassignActions
{
    public function desasignarInstructores()
    {
        // Guardar el conteo antes de limpiar la selección
        $conteoInstructores = count($this->selectedInstructoresAsignados);

        if (empty($this->selectedInstructoresAsignados)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Seleccione al menos un instructor para desasignar',
            ]);

            return;
        }

        try {
            \DB::beginTransaction();

            \Log::info('=== INICIO DESASIGNACIÓN INSTRUCTORES ===');
            \Log::info('Instructores a desasignar:', [
                'selectedInstructoresAsignados' => $this->selectedInstructoresAsignados,
                'count' => $conteoInstructores,
                'ficha_id' => $this->selectedFichaInstructores?->id,
            ]);

            foreach ($this->selectedInstructoresAsignados as $asignacionId) {
                \Log::info('Desasignando asignación:', ['asignacion_id' => $asignacionId]);

                // Eliminar la asignación del instructor
                $asignacion = \App\Models\InstructorFichaCaracterizacion::find($asignacionId);
                if ($asignacion) {
                    // Verificar que no sea el instructor principal
                    if ($this->selectedFichaInstructores->instructor_id == $asignacion->instructor_id) {
                        \Log::warning('No se puede desasignar al instructor principal:', [
                            'asignacion_id' => $asignacionId,
                            'instructor_id' => $asignacion->instructor_id,
                        ]);

                        continue;
                    }

                    $asignacion->delete();
                }
            }

            \DB::commit();

            \Log::info('Transacción confirmada - '.$conteoInstructores.' instructores desasignados');

            // Recargar la ficha para actualizar los instructores asignados
            $this->selectedFichaInstructores = \App\Models\FichaCaracterizacion::with([
                'instructor.persona',
                'instructorFicha.instructor.persona',
                'instructorFicha.instructorFichaDias.dia',
                'diasFormacion.dia',
                'programaFormacion.redConocimiento',
                'sede.regional',
                'jornadaFormacion.parametro',
            ])->find($this->selectedFichaInstructores->id);

            // Recargar instructores asignados
            $this->instructoresAsignados = $this->selectedFichaInstructores->instructorFicha()
                ->with(['competencia', 'resultadosAprendizaje'])
                ->with(['instructor.persona', 'instructorFichaDias.dia'])
                ->get();

            // Recargar instructores disponibles
            $this->loadInstructoresDisponibles();

            // Limpiar selección
            $this->reset(['selectedInstructoresAsignados', 'selectAllInstructoresAsignados']);

            // Forzar refresh de la vista
            $this->dispatch('refreshComponent');
            $this->dispatch('$refresh');

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $conteoInstructores.' instructores desasignados exitosamente',
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al desasignar instructores:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'selectedInstructoresAsignados' => $this->selectedInstructoresAsignados,
                'ficha_id' => $this->selectedFichaInstructores?->id,
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al desasignar instructores: '.$e->getMessage(),
            ]);
        }

        \Log::info('=== FIN DESASIGNACIÓN INSTRUCTORES ===');
    }
}
