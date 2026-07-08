<?php

namespace App\Livewire\Asistencia\Concerns;

use App\Models\Asistencia;
use App\Models\Evidencias;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesCrearEvidenciaModalStoreActions
{
    public function crearEvidencia()
    {
        try {
            Log::info('=== CREANDO EVIDENCIA Y ASISTENCIA ===');
            Log::info('Datos recibidos:', [
                'nombreEvidencia' => $this->nombreEvidencia,
                'fichaId' => $this->selectedFichaId,
                'userId' => Auth::id(),
            ]);

            $this->validate();

            $evidencia = Evidencias::create([
                'nombre' => $this->nombreEvidencia,
                'user_create_id' => Auth::id(),
                'user_edit_id' => Auth::id(),
                'fecha_evidencia' => now()->toDateString(),
                'id_estado' => 1,
            ]);

            Log::info('Evidencia creada exitosamente:', [
                'evidencia_id' => $evidencia->id,
                'nombre' => $evidencia->nombre,
                'fecha' => $evidencia->fecha_evidencia,
            ]);

            $asistencia = Asistencia::create([
                'evidencia_id' => $evidencia->id,
                'instructor_ficha_id' => $this->selectedFichaId,
                'fecha' => now()->toDateString(),
                'hora_inicio' => now(),
                'is_finished' => false,
                'user_create_id' => Auth::id(),
                'user_edit_id' => Auth::id(),
            ]);

            Log::info('Asistencia creada exitosamente:', [
                'asistencia_id' => $asistencia->id,
                'evidencia_id' => $asistencia->evidencia_id,
                'instructor_ficha_id' => $asistencia->instructor_ficha_id,
                'fecha' => $asistencia->fecha,
                'hora_inicio' => $asistencia->hora_inicio,
                'user_create_id' => $asistencia->user_create_id,
                'user_edit_id' => $asistencia->user_edit_id,
                'is_finished' => $asistencia->is_finished,
            ]);

            $this->closeModalEvidencia();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Evidencia y asistencia creadas correctamente. Redirigiendo...',
            ]);

            Log::info('Datos para redirección:', [
                'selectedFichaId' => $this->selectedFichaId,
                'asistencia_id' => $asistencia->id,
                'evidencia_id' => $evidencia->id,
            ]);

            return $this->redirect(route('asistence.caracterSelected', [
                'caracterizacion' => $this->selectedFichaId,
                'asistencia_id' => $asistencia->id,
            ]));
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                Log::warning('Nombre de evidencia duplicado: '.$this->nombreEvidencia);
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Ya existe una evidencia con este nombre. Por favor, usa un nombre diferente.',
                ]);
            } else {
                Log::error('Error de base de datos al crear evidencia/asistencia: '.$e->getMessage());
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Error de base de datos: '.$e->getMessage(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error al crear evidencia/asistencia: '.$e->getMessage());
            Log::error('Stack trace:', [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al crear la evidencia y asistencia: '.$e->getMessage(),
            ]);
        }
    }
}
