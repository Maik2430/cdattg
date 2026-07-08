<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;

trait HandlesGuiaAprendizajeGestionarResultadosConfirmActions
{
    public function confirmarDesasignacion($resultadoId)
    {
        $resultado = ResultadosAprendizaje::find($resultadoId);

        if (! $resultado) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Resultado de aprendizaje no encontrado',
            ]);

            return;
        }

        $this->dispatch('showGlobalConfirmModal', [
            'title' => 'Confirmar desasignación',
            'message' => '¿Deseas desasignar este resultado de la guía?',
            'type' => 'danger',
            'action' => 'desasignarResultado',
            'params' => ['resultadoId' => $resultadoId],
            'codigo' => $resultado->codigo,
            'nombre' => $resultado->nombre,
            'itemType' => 'Resultado de Aprendizaje',
        ]);
    }

    public function confirmarCierreGestion()
    {
        $this->showConfirmarCierre = true;
    }

    public function handleConfirmedAction($action, $params)
    {
        if ($action === 'desasignarResultado') {
            $this->desasignarResultado($params);
        } elseif ($action === 'asignarResultado') {
            $this->asignarResultadoDirecto($params);
        }
    }
}
