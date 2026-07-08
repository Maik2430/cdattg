<?php

namespace App\Livewire\Fichas\Concerns\FichaForm;

trait HandlesFichaFormMountHelpers
{
    public function mount($ficha = null, $isEdit = false): void
    {
        $this->isEdit = $isEdit;

        if ($ficha && $isEdit) {
            $this->ficha = $ficha;
            $this->loadFichaData();
        }

        $this->cargarDatosSelects();
    }
}
