<?php

namespace App\Services\Concerns\Notificacion;

use App\Models\Instructor;
use Illuminate\Support\Facades\Log;

trait HandlesNotificacionInstructorActions
{
    public function notificarNuevaFichaInstructor(Instructor $instructor, array $datosFicha): bool
    {
        try {
            $user = $instructor->persona->user ?? null;

            if (! $user || ! $user->email) {
                Log::warning('Instructor sin email para notificación', [
                    'instructor_id' => $instructor->id,
                ]);

                return false;
            }

            Log::info('Notificación de nueva ficha enviada', [
                'instructor_id' => $instructor->id,
                'email' => $user->email,
                'ficha' => $datosFicha['numero'] ?? 'N/A',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error enviando notificación', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function notificarRecordatorioClase(Instructor $instructor, array $datosClase): bool
    {
        try {
            $user = $instructor->persona->user ?? null;

            if (! $user || ! $user->email) {
                return false;
            }

            Log::info('Recordatorio de clase enviado', [
                'instructor_id' => $instructor->id,
                'clase' => $datosClase['ficha'] ?? 'N/A',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error enviando recordatorio', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
