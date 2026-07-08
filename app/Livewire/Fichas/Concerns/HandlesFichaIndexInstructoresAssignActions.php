<?php

namespace App\Livewire\Fichas\Concerns;

trait HandlesFichaIndexInstructoresAssignActions
{
    public function asignarInstructores()
    {
        // Guardar el conteo antes de limpiar la selección
        $conteoInstructores = count($this->selectedInstructores);

        \Log::info('=== INICIO ASIGNACIÓN INSTRUCTORES ===');
        \Log::info('Datos antes de asignar:', [
            'selectedInstructores' => $this->selectedInstructores,
            'count' => $conteoInstructores,
            'ficha_id' => $this->selectedFichaInstructores?->id,
        ]);

        if (empty($this->selectedInstructores)) {
            \Log::info('No hay instructores seleccionados - abortando');
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Seleccione al menos un instructor para asignar']);

            return;
        }

        try {
            \DB::beginTransaction();

            \Log::info('Iniciando transacción DB para asignar instructores');

            // Preparar datos para cada instructor seleccionado
            $instructoresData = [];
            foreach ($this->selectedInstructores as $index => $instructorId) {
                \Log::info('Asignando instructor '.($index + 1).':', ['instructor_id' => $instructorId]);

                $instructoresData[] = [
                    'instructor_id' => $instructorId,
                    'ficha_caracterizacion_id' => $this->selectedFichaInstructores->id,
                    'fecha_inicio' => $this->selectedFichaInstructores->fecha_inicio,
                    'fecha_fin' => $this->selectedFichaInstructores->fecha_fin,
                    'total_horas_instructor' => $this->selectedFichaInstructores->total_horas ?? 0,
                    'estado' => 1,
                    'user_create_id' => auth()->id(),
                    'user_edit_id' => auth()->id(),
                ];
            }

            // Usar el servicio especializado para la asignación
            $asignacionService = app(\App\Services\AsignacionInstructorService::class);
            $resultado = $asignacionService->asignarInstructores(
                $instructoresData,
                $this->selectedFichaInstructores->id,
                $this->selectedFichaInstructores->instructor_id ?? null,
                auth()->id()
            );

            \DB::commit();

            \Log::info('Transacción confirmada - '.$conteoInstructores.' instructores asignados');
            \Log::info('Resultado del servicio:', $resultado);

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
            $this->reset(['selectedInstructores', 'selectAllInstructores']);

            // Forzar refresh de la vista
            $this->dispatch('refreshComponent');
            $this->dispatch('$refresh');

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $conteoInstructores.' instructores asignados exitosamente',
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al asignar instructores:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'selectedInstructores' => $this->selectedInstructores,
                'ficha_id' => $this->selectedFichaInstructores?->id,
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al asignar instructores: '.$e->getMessage(),
            ]);
        }

        \Log::info('=== FIN ASIGNACIÓN INSTRUCTORES ===');
    }
}
