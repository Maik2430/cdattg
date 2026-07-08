<?php

namespace App\Livewire\Instructores\Concerns;

use App\Models\Instructor;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorIndexCrudActions
{
    public function deleteInstructor($instructorId)
    {
        try {
            $instructor = Instructor::find($instructorId);

            if (! $instructor) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Instructor no encontrado',
                ]);

                return;
            }

            $fichasAsignadas = $this->businessRulesService->contarTotalFichasAsignadas($instructor);

            if ($fichasAsignadas > 0) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'No se puede eliminar el instructor porque tiene '.$fichasAsignadas.' fichas asignadas',
                ]);

                return;
            }

            $codigo = $instructor->persona->numero_documento ?? $instructorId;

            $this->instructorService->eliminar($instructorId);

            $this->closeDeleteModal();

            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => "Instructor '{$codigo}' eliminado correctamente",
            ]);

            $this->dispatch('instructorEliminado');
        } catch (\Exception $e) {
            Log::error('Error eliminando instructor: '.$e->getMessage());

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar instructor: '.$e->getMessage(),
            ]);
        }
    }

    public function toggleStatus($instructorId)
    {
        try {
            $instructor = Instructor::find($instructorId);

            if (! $instructor) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Instructor no encontrado',
                ]);

                return;
            }

            $estadoActual = $instructor->status;

            $this->instructorService->cambiarEstado($instructorId, ! $estadoActual);

            $nuevoEstado = ! $estadoActual ? 'activado' : 'inactivado';

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Instructor {$nuevoEstado} correctamente",
            ]);
        } catch (\Exception $e) {
            Log::error('Error cambiando estado instructor: '.$e->getMessage());

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al cambiar estado: '.$e->getMessage(),
            ]);
        }
    }
}
