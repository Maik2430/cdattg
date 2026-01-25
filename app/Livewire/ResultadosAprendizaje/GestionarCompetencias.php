<?php

namespace App\Livewire\ResultadosAprendizaje;

use Livewire\Component;
use App\Models\ResultadoAprendizaje;
use App\Models\Competencia;

class GestionarCompetencias extends Component
{
    public $resultadoAprendizaje;
    public $competenciasAsignadas;
    public $competenciasDisponibles;

    protected $listeners = [
        'confirmAction' => 'handleConfirmedAction',
    ];

    public function mount($resultadoId)
    {
        $this->resultadoAprendizaje = ResultadoAprendizaje::findOrFail($resultadoId);
        $this->cargarCompetencias();
    }

    public function cargarCompetencias()
    {
        $this->competenciasAsignadas = $this->resultadoAprendizaje->competencias()->get();
        
        $asignadasIds = $this->competenciasAsignadas->pluck('id')->toArray();
        $this->competenciasDisponibles = Competencia::whereNotIn('id', $asignadasIds)
            ->orderBy('nombre')
            ->get();
    }

    public function confirmarAsociar($competenciaId, $nombreCompetencia)
    {
        $this->dispatch('confirm', [
            'title' => 'Asignar Competencia',
            'message' => "¿Desea asignar la competencia {$nombreCompetencia}?",
            'type' => 'info',
            'action' => 'asignarCompetencia',
            'params' => $competenciaId
        ]);
    }

    public function confirmarDesasociar($competenciaId, $nombreCompetencia)
    {
        $this->dispatch('confirm', [
            'title' => 'Desasociar Competencia',
            'message' => "¿Desea quitar la competencia {$nombreCompetencia}?",
            'type' => 'danger',
            'action' => 'desasociarCompetencia',
            'params' => $competenciaId
        ]);
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
        $this->resultadoAprendizaje->competencias()->attach($competenciaId, [
            'user_create_id' => auth()->id(),
            'user_edit_id' => auth()->id(),
        ]);
        
        $this->cargarCompetencias();
    }

    public function desasociarCompetencia($competenciaId)
    {
        $this->resultadoAprendizaje->competencias()->detach($competenciaId);
        
        $this->cargarCompetencias();
    }

    public function render()
    {
        return view('livewire.resultados-aprendizaje.gestionar-competencias');
    }
}
