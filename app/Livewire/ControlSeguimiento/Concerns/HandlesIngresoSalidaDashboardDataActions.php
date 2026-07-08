<?php

namespace App\Livewire\ControlSeguimiento\Concerns;

trait HandlesIngresoSalidaDashboardDataActions
{
    public function cargarDatos()
    {
        $this->sedes = $this->sedeRepository->obtenerActivas();
        $this->estadisticasPorSede = [];
        $fecha = $this->fechaSeleccionada ?? \Carbon\Carbon::today()->format('Y-m-d');

        $this->tieneFechaAnterior = $this->personaIngresoSalidaService
            ->obtenerFechaAnteriorConRegistros($fecha) !== null;
        $this->tieneFechaSiguiente = $this->personaIngresoSalidaService
            ->obtenerFechaSiguienteConRegistros($fecha) !== null;

        /** @var \App\Models\Sede $sede */
        foreach ($this->sedes as $sede) {
            $sedeId = (int) $sede->id;

            $estadisticasFecha = $this->personaIngresoSalidaService
                ->obtenerEstadisticasPersonasDentroPorFecha($fecha, $sedeId);
            $estadisticasGenerales = $this->personaIngresoSalidaService
                ->obtenerEstadisticasPersonasDentro($sedeId);

            $estadisticasRegistros = $this->personaIngresoSalidaService
                ->obtenerEstadisticasPorFecha($fecha, $sedeId);

            $this->estadisticasPorSede[$sedeId] = [
                'sede' => $sede,
                'estadisticas_hoy' => $estadisticasFecha,
                'estadisticas_generales' => $estadisticasGenerales,
                'estadisticas_registros' => $estadisticasRegistros,
                'tiene_registros_hoy' => ($estadisticasRegistros['entradas']['total'] > 0 ||
                    $estadisticasRegistros['salidas']['total'] > 0),
            ];
        }

        $this->estadisticasGenerales = $this->personaIngresoSalidaService
            ->obtenerEstadisticasPersonasDentroPorFecha($fecha);

        $this->estadisticasPorHora = $this->personaIngresoSalidaService
            ->obtenerEstadisticasPorHora($fecha);

        $this->eventosRecientes = $this->personaIngresoSalidaService
            ->obtenerEventosRecientes($fecha, null, 30);
    }

    public function actualizar()
    {
        $this->cargarDatos();
    }

    public function refrescar()
    {
        $this->cargarDatos();
        $this->dispatch('datos-actualizados');
    }
}
