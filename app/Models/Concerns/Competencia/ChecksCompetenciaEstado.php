<?php

namespace App\Models\Concerns\Competencia;

trait ChecksCompetenciaEstado
{
    public function isActivo()
    {
        return $this->status === 1 || $this->status === true;
    }

    public function duracionEnHoras()
    {
        return $this->duracion.' horas';
    }

    public function tieneFechasDefinidas()
    {
        return ! is_null($this->fecha_inicio) && ! is_null($this->fecha_fin);
    }

    public function estaVigente()
    {
        if (! $this->tieneFechasDefinidas()) {
            return false;
        }

        $hoy = now();

        return $this->fecha_inicio <= $hoy && $this->fecha_fin >= $hoy;
    }

    public function contarRAPsAsociados()
    {
        return $this->resultadosAprendizaje()->count();
    }

    public function rapActual()
    {
        foreach ($this->resultadosCompetencia as $rap) {
            if ($rap->rap && $rap->rap->status == 1) {
                return $rap->rap;
            }
        }

        return null;
    }
}
