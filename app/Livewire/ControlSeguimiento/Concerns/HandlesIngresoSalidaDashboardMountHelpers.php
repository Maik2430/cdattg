<?php

namespace App\Livewire\ControlSeguimiento\Concerns;

use App\Repositories\SedeRepository;
use App\Services\PersonaIngresoSalidaService;

trait HandlesIngresoSalidaDashboardMountHelpers
{
    public function boot(
        SedeRepository $sedeRepository,
        PersonaIngresoSalidaService $personaIngresoSalidaService
    ) {
        $this->sedeRepository = $sedeRepository;
        $this->personaIngresoSalidaService = $personaIngresoSalidaService;
    }

    public function mount()
    {
        $this->fechaSeleccionada = \Carbon\Carbon::today()->format('Y-m-d');
        $this->tiposPersona = $this->personaIngresoSalidaService->obtenerTiposPersona();
        $this->configuracionTiposPersona = $this->personaIngresoSalidaService->obtenerConfiguracionTiposPersona();

        $this->dispatch('cargar-frecuencia-desde-storage');

        $this->cargarDatos();
    }
}
