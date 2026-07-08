<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;
use Illuminate\Support\Facades\Log;

trait HandlesGuiaAprendizajeGestionarResultadosLoadHelpers
{
    public function cargarResultados()
    {
        if (! $this->guia) {
            $this->resultadosAsignados = collect();
            $this->resultadosDisponibles = collect();

            return;
        }

        try {
            $this->resultadosAsignados = $this->guia->resultadosAprendizaje()
                ->withPivot('created_at', 'user_create_id')
                ->orderBy('codigo')
                ->get();

            $asignadosIds = $this->resultadosAsignados->pluck('id')->toArray();

            $this->resultadosDisponibles = ResultadosAprendizaje::where('status', 1)
                ->whereNotIn('id', $asignadosIds)
                ->orderBy('codigo')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error en cargarResultados: '.$e->getMessage());
            $this->resultadosAsignados = collect();
            $this->resultadosDisponibles = collect();
        }
    }
}
