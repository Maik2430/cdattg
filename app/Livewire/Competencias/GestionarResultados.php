<?php

namespace App\Livewire\Competencias;

use Livewire\Component;
use App\Models\Competencia;
use App\Models\ResultadosAprendizaje;
use Livewire\WithPagination;

class GestionarResultados extends Component
{
    use WithPagination;

    public $competencia;
    public $searchAsignados = '';
    public $searchDisponibles = '';
    public $perPage = 10;
    public $selectedResultados = [];
    
    public $showAsociarModal = false;
    public $showDesasociarModal = false;
    public $selectedResultado = null;

    protected $queryString = [
        'searchAsignados' => ['except' => ''],
        'searchDisponibles' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected $listeners = [
        'resultadoAsociado' => '$refresh',
        'resultadoDesasociado' => '$refresh',
        'closeModal' => 'handleCloseModal',
        'notify' => 'showNotification',
    ];

    public function mount(Competencia $competencia)
    {
        $this->competencia = $competencia;
        \Log::info('GestionarResultados mounted for competencia: ' . $competencia->id);
    }

    public function render()
    {
        // Obtener resultados asignados
        $resultadosAsignadosQuery = $this->competencia->resultadosAprendizaje();
        
        if ($this->searchAsignados) {
            $resultadosAsignadosQuery->where(function ($query) {
                $query->where('codigo', 'like', '%' . $this->searchAsignados . '%')
                      ->orWhere('nombre', 'like', '%' . $this->searchAsignados . '%');
            });
        }
        
        $resultadosAsignados = $resultadosAsignadosQuery->paginate($this->perPage);

        // Obtener resultados disponibles
        $resultadosDisponiblesQuery = ResultadosAprendizaje::where('status', 1)
            ->whereNotIn('resultados_aprendizajes.id', $this->competencia->resultadosAprendizaje()->pluck('resultados_aprendizajes.id'));
        
        if ($this->searchDisponibles) {
            $resultadosDisponiblesQuery->where(function ($query) {
                $query->where('codigo', 'like', '%' . $this->searchDisponibles . '%')
                      ->orWhere('nombre', 'like', '%' . $this->searchDisponibles . '%');
            });
        }
        
        $resultadosDisponibles = $resultadosDisponiblesQuery->get();

        // Calcular estadísticas - Ahora que la columna debería existir
        $duracionTotal = \DB::table('resultados_aprendizaje_competencia')
            ->where('competencia_id', $this->competencia->id)
            ->sum('duracion');

        // Calcular estadísticas
        $totalAsignados = $this->competencia->resultadosAprendizaje()->count();
        $totalDisponibles = $resultadosDisponibles->count();

        return view('livewire.competencias.gestionar-resultados', compact(
            'resultadosAsignados',
            'resultadosDisponibles',
            'totalAsignados',
            'totalDisponibles',
            'duracionTotal'
        ));
    }

    public function openAsociarModal()
    {
        $this->showAsociarModal = true;
        $this->selectedResultados = [];
    }

    public function closeAsociarModal()
    {
        $this->showAsociarModal = false;
        $this->selectedResultados = [];
    }

    public function openDesasociarModal($resultadoId)
    {
        $this->selectedResultado = ResultadosAprendizaje::find($resultadoId);
        $this->showDesasociarModal = true;
    }

    public function closeDesasociarModal()
    {
        $this->showDesasociarModal = false;
        $this->selectedResultado = null;
    }

    public function handleCloseModal()
    {
        $this->showAsociarModal = false;
        $this->showDesasociarModal = false;
        $this->selectedResultados = [];
        $this->selectedResultado = null;
    }

    public function showNotification($data)
    {
        // Este método maneja las notificaciones desde el backend
    }

    public function asociarResultados()
    {
        if (empty($this->selectedResultados)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Debe seleccionar al menos un resultado de aprendizaje'
            ]);
            return;
        }

        try {
            foreach ($this->selectedResultados as $resultadoId) {
                $this->competencia->resultadosAprendizaje()->attach($resultadoId, [
                    'duracion' => 0, // Valor por defecto, puede ser ajustado después
                    'user_create_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->closeAsociarModal();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => count($this->selectedResultados) . ' resultado(s) asociado(s) correctamente'
            ]);
            $this->dispatch('resultadoAsociado');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al asociar resultados: ' . $e->getMessage()
            ]);
        }
    }

    public function desasociarResultado($resultadoId)
    {
        try {
            $this->competencia->resultadosAprendizaje()->detach($resultadoId);

            $this->closeDesasociarModal();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Resultado desasociado correctamente'
            ]);
            $this->dispatch('resultadoDesasociado');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al desasociar resultado: ' . $e->getMessage()
            ]);
        }
    }

    public function asociarResultadoDirecto($resultadoId)
    {
        try {
            $this->competencia->resultadosAprendizaje()->attach($resultadoId, [
                'duracion' => 0, // Valor por defecto
                'user_create_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Resultado asociado correctamente'
            ]);
            $this->dispatch('resultadoAsociado');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al asociar resultado: ' . $e->getMessage()
            ]);
        }
    }

    public function updatingSearchAsignados()
    {
        $this->resetPage();
    }

    public function updatingSearchDisponibles()
    {
        // No resetear página para disponibles ya que no tienen paginación
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function formatearHoras($horas)
    {
        if ($horas == 0) {
            return '0';
        }
        
        return number_format($horas, 0, ',', '.');
    }
}
