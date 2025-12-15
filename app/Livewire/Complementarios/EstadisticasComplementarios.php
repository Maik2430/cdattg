<?php

declare(strict_types=1);

namespace App\Livewire\Complementarios;

use App\Services\Complementarios\EstadisticaComplementarioService;
use Livewire\Component;

class EstadisticasComplementarios extends Component
{
    public int $totalAspirantes = 0;
    public int $aspirantesAceptados = 0;
    public int $aspirantesPendientes = 0;
    public int $programasActivos = 0;
    public array $tendenciaInscripciones = [];
    public array $distribucionProgramas = [];
    public array $programasDemanda = [];

    protected EstadisticaComplementarioService $estadisticaService;

    public function boot(EstadisticaComplementarioService $estadisticaService): void
    {
        $this->estadisticaService = $estadisticaService;
    }

    public function mount(): void
    {
        $this->cargarDatos();
    }

    /**
     * Carga todos los datos de estadísticas
     */
    public function cargarDatos(): void
    {
        $estadisticas = $this->estadisticaService->obtenerEstadisticasReales();

        $this->totalAspirantes = $estadisticas['total_aspirantes'];
        $this->aspirantesAceptados = $estadisticas['aspirantes_aceptados'];
        $this->aspirantesPendientes = $estadisticas['aspirantes_pendientes'];
        $this->programasActivos = $estadisticas['programas_activos'];
        
        // Convertir colecciones a arrays para Livewire
        $this->tendenciaInscripciones = $estadisticas['tendencia_inscripciones']->map(function ($item) {
            return [
                'year' => $item->year ?? $item['year'] ?? null,
                'month' => $item->month ?? $item['month'] ?? null,
                'total' => $item->total ?? $item['total'] ?? 0,
            ];
        })->toArray();
        
        $this->distribucionProgramas = $estadisticas['distribucion_programas']->map(function ($item) {
            return [
                'programa' => $item->programa ?? $item['programa'] ?? '',
                'total' => $item->total ?? $item['total'] ?? 0,
            ];
        })->toArray();
        
        $this->programasDemanda = $estadisticas['programas_demanda']->toArray();
        
        // Disparar evento para actualizar gráficos
        $this->dispatch('estadisticas-actualizadas');
    }

    /**
     * Refrescar manualmente los datos
     */
    public function refrescar(): void
    {
        $this->cargarDatos();
        $this->dispatch('estadisticas-actualizadas');
    }

    public function render()
    {
        return view('livewire.complementarios.estadisticas-complementarios');
    }
}

