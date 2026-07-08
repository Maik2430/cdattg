<?php

namespace App\Models\Concerns\Competencia;

trait BuildsCompetenciaAttributes
{
    public function getEstadoFormateadoAttribute()
    {
        return $this->status ? 'ACTIVA' : 'INACTIVA';
    }

    public function getNombreCompletoAttribute()
    {
        return $this->codigo.' - '.$this->nombre;
    }

    public function getDuracionFormateadaAttribute()
    {
        return number_format($this->duracion ?? 0, 0).' horas';
    }

    public function getFechaInicioFormateadaAttribute()
    {
        return $this->fecha_inicio ? $this->fecha_inicio->format('d/m/Y') : 'N/A';
    }

    public function getFechaFinFormateadaAttribute()
    {
        return $this->fecha_fin ? $this->fecha_fin->format('d/m/Y') : 'N/A';
    }
}
