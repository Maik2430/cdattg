<?php

namespace App\Livewire\Aprendices\Concerns;

trait HandlesAprendizFormMountHelpers
{
    public function mount($aprendiz = null, $isEdit = false)
    {
        $this->fichas = collect();
        $this->personas = collect();
        $this->isEdit = $isEdit;

        if ($aprendiz && $isEdit) {
            $this->aprendiz = $aprendiz;
            $this->loadAprendizData();
        }

        $this->cargarDatosSelects();
    }

    private function loadAprendizData()
    {
        if (! $this->aprendiz) {
            return;
        }

        $this->persona_id = $this->aprendiz->persona_id;
        $this->ficha_caracterizacion_id = $this->aprendiz->ficha_caracterizacion_id;
        $this->estado = (int) $this->aprendiz->estado;
    }
}
