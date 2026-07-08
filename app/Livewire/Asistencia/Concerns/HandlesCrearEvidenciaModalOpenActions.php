<?php

namespace App\Livewire\Asistencia\Concerns;

use App\Models\Asistencia;
use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\Log;

trait HandlesCrearEvidenciaModalOpenActions
{
    public function openModalEvidencia(int|string $fichaId): void
    {
        try {
            $asistenciaActiva = Asistencia::where('instructor_ficha_id', $fichaId)
                ->where('is_finished', false)
                ->first();

            if ($asistenciaActiva) {
                $this->redirect(route('asistence.caracterSelected', [
                    'caracterizacion' => $fichaId,
                    'asistencia_id' => $asistenciaActiva->id,
                ]));

                return;
            }

            $this->selectedFichaId = $fichaId;
            $this->selectedFicha = FichaCaracterizacion::with([
                'programaFormacion',
                'instructor.persona',
            ])->find($fichaId);

            if (! $this->selectedFicha) {
                Log::error('Ficha no encontrada al abrir modal de evidencia', ['ficha_id' => $fichaId]);
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Ficha no encontrada',
                ]);

                return;
            }

            $this->reset(['nombreEvidencia']);
            $this->showModalEvidencia = true;
        } catch (\Exception $e) {
            Log::error('Error al verificar asistencia activa', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al verificar asistencia activa: '.$e->getMessage(),
            ]);
        }
    }

    public function closeModalEvidencia(): void
    {
        $this->showModalEvidencia = false;
        $this->reset(['nombreEvidencia']);
    }
}
