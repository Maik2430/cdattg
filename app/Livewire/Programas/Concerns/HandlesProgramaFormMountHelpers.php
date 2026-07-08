<?php

namespace App\Livewire\Programas\Concerns;

use App\Models\ProgramaFormacion;

trait HandlesProgramaFormMountHelpers
{
    public function updated($property)
    {
        if (in_array($property, [
            'horas_totales',
            'horas_etapa_lectiva',
            'horas_etapa_productiva',
        ])) {
            $this->horas_validas =
                ($this->horas_etapa_lectiva + $this->horas_etapa_productiva)
                === $this->horas_totales;
        }
    }

    public function mount($programaId = null)
    {
        if ($programaId) {
            $this->isEdit = true;
            $this->loadPrograma($programaId);
        }
    }

    public function loadPrograma($programaId)
    {
        $programa = ProgramaFormacion::find($programaId);
        if ($programa) {
            $this->programaId = $programa->id;
            $this->isEdit = true;
            $this->codigo = (string) $programa->codigo;
            $this->nombre = $programa->nombre;
            $this->red_conocimiento_id = $programa->red_conocimiento_id;
            $this->nivel_formacion_id = $programa->nivel_formacion_id;
            $this->horas_totales = $programa->horas_totales;
            $this->horas_etapa_lectiva = $programa->horas_etapa_lectiva;
            $this->horas_etapa_productiva = $programa->horas_etapa_productiva;
        }
    }
}
