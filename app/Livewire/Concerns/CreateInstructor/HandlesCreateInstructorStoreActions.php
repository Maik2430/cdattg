<?php

namespace App\Livewire\Concerns\CreateInstructor;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesCreateInstructorStoreActions
{
    use HandlesCreateInstructorStoreDataHelpers;

    public function store(): void
    {
        try {
            Log::info('Datos antes de validar en CreateInstructor', [
                'modalidades' => $this->modalidades,
                'modalidades_tipo' => gettype($this->modalidades),
                'modalidades_count' => is_array($this->modalidades) ? count($this->modalidades) : 0,
                'jornadas' => $this->jornadas,
                'especialidades' => $this->especialidades,
            ]);

            $datos = $this->validate();

            Log::info('Datos después de validar en CreateInstructor', [
                'modalidades_en_datos' => $datos['modalidades'] ?? 'no existe',
                'modalidades_propiedad' => $this->modalidades,
            ]);

            if (! empty($this->especialidades)) {
                $datos['especialidades'] = $this->especialidades;
            }

            $jornadasIds = $this->prepareJornadasForStore($datos);
            $this->prepareJsonArrayFieldsForStore($datos);
            $this->prepareAreasExperticiaForStore($datos);
            $this->prepareCompetenciasTicForStore($datos);

            $datos['user_create_id'] = Auth::id();

            $modalidadesIds = $this->prepareModalidadesIdsForStore();

            $this->instructorService->crear($datos, $jornadasIds, $modalidadesIds);

            session()->flash('success', '¡Instructor asignado exitosamente!');

            $this->redirect(route('instructor.index'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al crear instructor desde Livewire: '.$e->getMessage());

            session()->flash('error', $e->getMessage());

            $this->addError('general', $e->getMessage());
        }
    }
}
