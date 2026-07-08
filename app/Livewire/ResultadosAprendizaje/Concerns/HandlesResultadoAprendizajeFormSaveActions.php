<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

trait HandlesResultadoAprendizajeFormSaveActions
{
    use HandlesResultadoAprendizajeFormStoreActions;
    use HandlesResultadoAprendizajeFormUpdateActions;

    public function save()
    {
        if ($this->isEdit) {
            $this->rules['codigo'] = 'required|string|max:20|unique:resultados_aprendizajes,codigo,'.$this->resultadoId;
        }

        $this->validate();

        try {
            if ($this->isEdit) {
                if (! $this->updateResultadoAprendizaje()) {
                    return;
                }
            } else {
                $this->storeResultadoAprendizaje();
            }

            $this->cancel();
        } catch (\Exception $e) {
            \Log::error('Error al guardar resultado de aprendizaje: '.$e->getMessage());
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al guardar el resultado: '.$e->getMessage(),
            ]);
        }
    }
}
