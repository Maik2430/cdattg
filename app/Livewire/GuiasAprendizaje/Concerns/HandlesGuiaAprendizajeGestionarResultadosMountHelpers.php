<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\GuiasAprendizaje;

trait HandlesGuiaAprendizajeGestionarResultadosMountHelpers
{
    public function boot()
    {
        $this->resultadosAsignados = collect();
        $this->resultadosDisponibles = collect();
    }

    public function abrirModal($data)
    {
        $guiaId = $data['guiaId'] ?? null;

        if (! $guiaId) {
            return;
        }

        $this->guia = GuiasAprendizaje::with(['resultadosAprendizaje' => function ($query) {
            $query->withPivot('created_at', 'user_create_id');
        }])->find($guiaId);

        if (! $this->guia) {
            return;
        }

        $this->cargarResultados();
        $this->dispatch('showGestionarResultadosModal');
    }

    public function mount($guia = null)
    {
        if ($guia) {
            $this->guia = $guia;
            $this->cargarResultados();
        }
    }
}
