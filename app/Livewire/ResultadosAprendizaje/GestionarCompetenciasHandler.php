<?php

namespace App\Livewire\ResultadosAprendizaje;

use Livewire\Component;
use App\Models\ResultadosAprendizaje;
use App\Models\Competencia;

class GestionarCompetenciasHandler extends Component
{
    public $resultadoId;
    public $resultado;
    public $competenciasAsignadas;
    public $competenciasDisponibles;

    protected $listeners = [
        'confirmAction' => 'handleConfirmedAction',
    ];

    public function mount()
    {
        // Obtener el ID del resultado desde la URL actual
        $this->resultadoId = request()->segment(2); // /resultados-aprendizaje/{id}/gestionar-competencias
        $this->refreshData();
    }

    public function handleConfirmedAction($action, $params)
    {
        try {
            switch ($action) {
                case 'asignarCompetencia':
                    $this->asignarCompetencia($params);
                    break;
                case 'desasociarCompetencia':
                    $this->desasociarCompetencia($params);
                    break;
            }
            
            // 🔥 CLAVE: Refrescar datos después de modificar BD
            $this->refreshData();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Operación completada correctamente'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al ejecutar la acción: ' . $e->getMessage()
            ]);
        }
    }

    public function asignarCompetencia($competenciaId)
    {
        $resultado = ResultadosAprendizaje::findOrFail($this->resultadoId);
        
        $resultado->competencias()->attach($competenciaId, [
            'user_create_id' => auth()->id(),
            'user_edit_id' => auth()->id(),
        ]);
    }

    public function desasociarCompetencia($competenciaId)
    {
        $resultado = ResultadosAprendizaje::findOrFail($this->resultadoId);
        
        $resultado->competencias()->detach($competenciaId);
    }

    /**
     * 🔥 CLAVE: Refrescar las propiedades públicas para que Livewire re-renderice
     */
    public function refreshData()
    {
        $this->resultado = ResultadosAprendizaje::findOrFail($this->resultadoId);
        $this->competenciasAsignadas = $this->resultado->competencias()->get();
        
        $this->competenciasDisponibles = Competencia::whereNotIn('id', $this->competenciasAsignadas->pluck('id'))
            ->orderBy('nombre')
            ->get();
    }

    public function render()
    {
        return view('livewire.resultados-aprendizaje.gestionar-competencias-handler');
    }
}
