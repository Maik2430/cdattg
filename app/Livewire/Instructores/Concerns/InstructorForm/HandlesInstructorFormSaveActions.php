<?php

namespace App\Livewire\Instructores\Concerns\InstructorForm;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait HandlesInstructorFormSaveActions
{
    use HandlesInstructorFormPrepareDataHelpers;

    public function save(): void
    {
        try {
            Log::info('[InstructorForm] Intentando guardar instructor', [
                'isEdit' => $this->isEdit,
                'persona_id' => $this->persona_id,
                'regional_id' => $this->regional_id,
            ]);

            $this->validate();

            if (! $this->isEdit && ($this->personasDisponibles->isEmpty() || ! $this->persona_id)) {
                Log::warning('[InstructorForm] No hay personas disponibles para crear instructor.', [
                    'personasDisponibles' => $this->personasDisponibles,
                    'persona_id' => $this->persona_id,
                ]);
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'No hay personas disponibles para crear instructor.',
                ]);

                return;
            }

            $datos = $this->prepareDataForService();
            Log::info('[InstructorForm] Datos preparados para guardar', $datos);

            if ($this->isEdit) {
                $this->instructorService->actualizar($this->instructor->id, $datos);

                Log::info('[InstructorForm] Instructor actualizado correctamente', [
                    'instructor_id' => $this->instructor->id,
                ]);

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Instructor actualizado correctamente',
                ]);
                $this->dispatch('instructorActualizado');
            } else {
                $instructor = $this->instructorService->crear($datos, $this->jornadas);

                Log::info('[InstructorForm] Instructor creado correctamente', [
                    'instructor_id' => $instructor->id ?? null,
                ]);

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Instructor creado correctamente',
                ]);
                $this->dispatch('instructorCreado');
            }

            $this->dispatch('closeModal');
        } catch (ValidationException $e) {
            Log::error('[InstructorForm] Error de validación', [
                'errors' => $e->errors(),
                'message' => $e->getMessage(),
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error de validación: '.$e->getMessage(),
            ]);
        } catch (Exception $e) {
            Log::error('[InstructorForm] Error guardando instructor', [
                'exception' => $e,
                'message' => $e->getMessage(),
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al guardar instructor: '.$e->getMessage(),
            ]);
        }
    }
}
