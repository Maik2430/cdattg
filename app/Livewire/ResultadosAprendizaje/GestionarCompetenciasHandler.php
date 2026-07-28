<?php

namespace App\Livewire\ResultadosAprendizaje;

use App\Models\Competencia;
use App\Models\ResultadosAprendizaje;
use Livewire\Component;

class GestionarCompetenciasHandler extends Component
{
    public $resultadoId;

    public $resultado;

    public $competenciasAsignadas;

    public $competenciasDisponibles;

    protected $listeners = [
        'confirmAction' => 'handleConfirmedAction',
    ];

    public function mount($resultadoId = null)
    {
        // Obtener el ID del resultado desde parámetro o la URL actual
        // /resultados-aprendizaje/{id}/gestionar-competencias
        $this->resultadoId = $resultadoId ?? request()->segment(2);
        $this->refreshData();
    }

    public function handleConfirmedAction($action, $params)
    {
        try {
            if ($action === 'asignarCompetencia') {
                $this->asignarCompetencia($params);
            } elseif ($action === 'desasociarCompetencia') {
                $this->desasociarCompetencia($params);
            } else {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Acción no reconocida',
                ]);

                return;
            }

            // 🔥 CLAVE: Refrescar datos después de modificar BD
            $this->refreshData();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Operación completada correctamente',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al ejecutar la acción: '.$e->getMessage(),
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
