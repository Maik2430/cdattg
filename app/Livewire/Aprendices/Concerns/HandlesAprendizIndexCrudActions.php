<?php

namespace App\Livewire\Aprendices\Concerns;

use App\Models\Aprendiz;

trait HandlesAprendizIndexCrudActions
{
    public function deleteAprendiz($aprendizId)
    {
        $aprendiz = Aprendiz::find($aprendizId);

        if (! $aprendiz) {
            return;
        }

        try {
            $aprendiz->delete();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Aprendiz eliminado correctamente',
            ]);

            $this->dispatch('aprendizEliminado');
            $this->closeDeleteModal();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar el aprendiz: '.$e->getMessage(),
            ]);
        }
    }

    public function toggleStatus($aprendizId)
    {
        $aprendiz = Aprendiz::find($aprendizId);

        if (! $aprendiz) {
            return;
        }

        try {
            $aprendiz->estado = ! $aprendiz->estado;
            $aprendiz->save();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $aprendiz->estado ? 'Aprendiz activado correctamente' : 'Aprendiz desactivado correctamente',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al cambiar estado: '.$e->getMessage(),
            ]);
        }
    }

    public function obtenerEstadoFormateado($aprendiz)
    {
        return [
            'status' => $aprendiz->estado,
            'texto' => $aprendiz->estado ? 'Activo' : 'Inactivo',
            'clase' => $aprendiz->estado ? 'success' : 'danger',
        ];
    }
}
