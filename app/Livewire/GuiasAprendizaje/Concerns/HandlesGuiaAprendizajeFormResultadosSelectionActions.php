<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

trait HandlesGuiaAprendizajeFormResultadosSelectionActions
{
    public function updatedSearchResultado()
    {
        $this->cargarResultadosDisponibles();
    }

    public function agregarResultado($resultadoId)
    {
        if (! in_array($resultadoId, $this->resultadosSeleccionados)) {
            $this->resultadosSeleccionados[] = $resultadoId;
            $this->cargarResultadosDisponibles();
        }
    }

    public function quitarResultado($resultadoId)
    {
        $key = array_search($resultadoId, $this->resultadosSeleccionados);
        if ($key !== false) {
            unset($this->resultadosSeleccionados[$key]);
            $this->resultadosSeleccionados = array_values($this->resultadosSeleccionados);
            $this->cargarResultadosDisponibles();
        }
    }

    public function limpiarSeleccion()
    {
        $this->resultadosSeleccionados = [];
        $this->selectAll = false;
        $this->cargarResultadosDisponibles();
    }

    public function seleccionarTodos()
    {
        $this->resultadosSeleccionados = $this->resultadosDisponibles->pluck('id')->toArray();
        $this->selectAll = true;
        $this->cargarResultadosDisponibles();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->seleccionarTodos();
        } else {
            $this->limpiarSeleccion();
        }
    }
}
