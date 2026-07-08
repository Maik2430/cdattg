<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use Illuminate\Support\Facades\DB;

trait HandlesGuiaAprendizajeFormSaveActions
{
    public function save()
    {
        $this->validate($this->guiaAprendizajeFormValidationRules());

        try {
            DB::beginTransaction();

            if ($this->isEdit) {
                $message = $this->updateGuiaAprendizaje();
                $this->dispatch('guiaActualizada');
            } else {
                $message = $this->storeGuiaAprendizaje();
                $this->dispatch('guiaCreada');
            }

            DB::commit();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message,
            ]);

            $this->dispatch('closeModal');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al guardar la guía: '.$e->getMessage(),
            ]);
        }
    }
}
