<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;

trait HandlesGuiaAprendizajeFormMountHelpers
{
    public function mount($guia = null)
    {
        if ($guia) {
            $this->guia = $guia;
            $this->isEdit = true;
            $this->loadGuiaData();
        }

        $this->resultadosAprendizaje = ResultadosAprendizaje::orderBy('codigo')->get();
        $this->cargarResultadosDisponibles();
    }

    private function loadGuiaData()
    {
        if (! $this->guia) {
            return;
        }

        $this->codigo = $this->guia->codigo;
        $this->nombre = $this->guia->nombre;
        $this->descripcion = $this->guia->descripcion;
        $this->programa_formacion_id = $this->guia->programa_formacion_id;
        $this->duracion_horas = $this->guia->duracion_horas;
        $this->duracion_meses = $this->guia->duracion_meses;
        $this->objetivo_general = $this->guia->objetivo_general;
        $this->metodologia = $this->guia->metodologia;
        $this->evaluacion = $this->guia->evaluacion;
        $this->status = $this->guia->status;

        $this->resultadosSeleccionados = $this->guia->resultadosAprendizaje()
            ->pluck('resultados_aprendizajes.id')
            ->toArray();
    }
}
