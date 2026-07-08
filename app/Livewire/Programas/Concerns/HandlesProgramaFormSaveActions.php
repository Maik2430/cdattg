<?php

namespace App\Livewire\Programas\Concerns;

trait HandlesProgramaFormSaveActions
{
    public function save()
    {
        if ($this->isEdit) {
            $this->update();
        } else {
            $this->store();
        }
    }

    public function cancel()
    {
        $this->dispatch('closeModal');
    }
}
