<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

use App\Models\Competencia;

trait HandlesResultadoAprendizajeFormRenderActions
{
    public function cancel()
    {
        $this->reset();
        $this->resetValidation();
        $this->mount();
        $this->dispatch('closeModal');
    }

    public function render()
    {
        $competencias = Competencia::orderBy('nombre')->get();

        return view('livewire.resultados-aprendizaje.resultado-aprendizaje-form', compact('competencias'));
    }
}
