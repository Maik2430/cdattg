<?php

namespace App\Services\Concerns\AsistenceQr;

use Illuminate\Support\Facades\Log;

trait HandlesAsistenceQrInstructorActions
{
    public function getInstructorFichaIndex($user)
    {
        Log::info('=== DEBUG ASISTENCEQRSERVICE GETINSTRUCTORFICHAINDEX ===');
        Log::info('User persona_id: '.($user->persona_id ?? 'NULL'));

        $roleNames = $user?->getRoleNames() ?? collect();
        $isOnlyInstructor = $user && $user->hasRole('INSTRUCTOR') && $roleNames->count() === 1;

        $instructor = $this->instructorRepository->getInstructor($user->persona_id);

        Log::info('Instructor encontrado: '.($instructor ? 'SI' : 'NO'));
        if ($instructor) {
            Log::info('Instructor ID: '.$instructor->id);
            Log::info('Instructor persona_id: '.$instructor->persona_id);
        }

        if (! $instructor) {
            Log::warning('No se encontró instructor para el usuario');

            return null;
        }

        $fichas = $this->instructorFichaCaracterizacionRepository->getInstructorFichaCaracterizacion(
            $instructor->id,
            $isOnlyInstructor
        );

        Log::info('Fichas obtenidas del repositorio: '.($fichas ? 'TIENE DATOS' : 'NULL'));
        if ($fichas) {
            Log::info('Cantidad de fichas desde repositorio: '.$fichas->count());
        }

        Log::info('=== FIN DEBUG SERVICE ===');

        return $fichas;
    }

    public function getDiasFormacion()
    {
        return $this->parametroRepository->getDiasFormacion();
    }
}
