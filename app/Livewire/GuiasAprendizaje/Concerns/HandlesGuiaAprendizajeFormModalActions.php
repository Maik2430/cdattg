<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

trait HandlesGuiaAprendizajeFormModalActions
{
    public function handleCloseModal()
    {
        $this->reset([
            'codigo', 'nombre', 'descripcion', 'programa_formacion_id',
            'duracion_horas', 'duracion_meses', 'objetivo_general',
            'metodologia', 'evaluacion', 'status',
        ]);
        $this->resultadosSeleccionados = [];
        $this->searchResultado = '';
        $this->cargarResultadosDisponibles();
    }
}
