<?php

namespace App\Livewire\ControlSeguimiento\Concerns;

use Carbon\Carbon;

trait HandlesIngresoSalidaDashboardFechaActions
{
    public function fechaAnterior(): void
    {
        $fechaAnterior = $this->personaIngresoSalidaService
            ->obtenerFechaAnteriorConRegistros($this->fechaSeleccionada);

        if ($fechaAnterior) {
            $this->fechaSeleccionada = $fechaAnterior;
            $this->cargarDatos();
        }
    }

    public function fechaSiguiente(): void
    {
        $fechaSiguiente = $this->personaIngresoSalidaService
            ->obtenerFechaSiguienteConRegistros($this->fechaSeleccionada);

        if ($fechaSiguiente) {
            $this->fechaSeleccionada = $fechaSiguiente;
            $this->cargarDatos();
        }
    }

    public function irAHoy(): void
    {
        $this->fechaSeleccionada = Carbon::today()->format('Y-m-d');
        $this->cargarDatos();
    }

    public function updatedFechaSeleccionada(string $value): void
    {
        $fechaCarbon = Carbon::parse($value);
        $hoy = Carbon::today();

        if ($fechaCarbon->isAfter($hoy)) {
            $this->fechaSeleccionada = $hoy->format('Y-m-d');
        } else {
            $this->fechaSeleccionada = $this->resolverFechaConRegistros($fechaCarbon);
        }

        $this->cargarDatos();
    }

    private function resolverFechaConRegistros(Carbon $fechaCarbon): string
    {
        $fechaFormateada = $fechaCarbon->format('Y-m-d');

        if ($this->personaIngresoSalidaService->fechaTieneRegistros($fechaFormateada)) {
            return $fechaFormateada;
        }

        $fechaAnterior = $this->personaIngresoSalidaService
            ->obtenerFechaAnteriorConRegistros($fechaFormateada);
        $fechaSiguiente = $this->personaIngresoSalidaService
            ->obtenerFechaSiguienteConRegistros($fechaFormateada);

        if ($fechaAnterior && $fechaSiguiente) {
            $diffAnterior = abs($fechaCarbon->diffInDays(Carbon::parse($fechaAnterior)));
            $diffSiguiente = abs($fechaCarbon->diffInDays(Carbon::parse($fechaSiguiente)));

            return $diffAnterior <= $diffSiguiente ? $fechaAnterior : $fechaSiguiente;
        }

        if ($fechaAnterior) {
            return $fechaAnterior;
        }

        if ($fechaSiguiente) {
            return $fechaSiguiente;
        }

        return Carbon::today()->format('Y-m-d');
    }
}
