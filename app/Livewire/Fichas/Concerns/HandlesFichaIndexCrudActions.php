<?php

namespace App\Livewire\Fichas\Concerns;

use App\Models\FichaCaracterizacion;

trait HandlesFichaIndexCrudActions
{
    public function toggleStatus($fichaId)
    {
        try {
            $ficha = FichaCaracterizacion::find($fichaId);
            if ($ficha) {
                $ficha->status = ! $ficha->status;
                $ficha->save();

                $this->dispatch('notify', [
                    'type' => $ficha->status ? 'success' : 'warning',
                    'message' => $ficha->status ? 'Ficha activada exitosamente' : 'Ficha desactivada',
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', 'error', 'Error al cambiar el estado de la ficha');
        }
    }

    public function deleteFicha($fichaId)
    {
        try {
            $ficha = FichaCaracterizacion::find($fichaId);
            if ($ficha) {
                // Verificar si tiene aprendices asociados
                if ($ficha->aprendices()->count() > 0) {
                    $this->dispatch('notify', ['type' => 'warning', 'message' => 'No se puede eliminar la ficha porque tiene aprendices asociados']);

                    return;
                }

                $ficha->delete();
                $this->dispatch('fichaEliminada');
                $this->dispatch('notify', ['type' => 'success', 'message' => 'Ficha eliminada exitosamente']);
                $this->closeDeleteModal();
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error al eliminar la ficha']);
        }
    }
}
