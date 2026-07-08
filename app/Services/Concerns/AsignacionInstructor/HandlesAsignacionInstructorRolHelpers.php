<?php

namespace App\Services\Concerns\AsignacionInstructor;

use App\Models\Instructor;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

trait HandlesAsignacionInstructorRolHelpers
{
    protected function asignarRolInstructorSiCorresponde(int $instructorId, int $fichaId, string $contexto): void
    {
        $instructor = Instructor::with('persona.user')->find($instructorId);

        if (! $instructor || ! $instructor->persona || ! $instructor->persona->user) {
            return;
        }

        Role::firstOrCreate(['name' => 'INSTRUCTOR']);

        if ($instructor->persona->user->hasRole('INSTRUCTOR')) {
            return;
        }

        $instructor->persona->user->assignRole('INSTRUCTOR');

        Log::info('Rol INSTRUCTOR asignado al usuario al '.$contexto, [
            'user_id' => $instructor->persona->user->id,
            'persona_id' => $instructor->persona_id,
            'instructor_id' => $instructor->id,
            'ficha_id' => $fichaId,
        ]);
    }

    protected function removerRolInstructorSiSinAsignaciones(int $instructorId, int $fichaId, int $asignacionId): void
    {
        $instructor = Instructor::with('persona.user')->find($instructorId);

        if (! $instructor) {
            return;
        }

        $tieneOtrasAsignaciones = \App\Models\InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
            ->where('id', '!=', $asignacionId)
            ->exists();

        if ($tieneOtrasAsignaciones || ! $instructor->persona || ! $instructor->persona->user) {
            return;
        }

        if (! $instructor->persona->user->hasRole('INSTRUCTOR')) {
            return;
        }

        $instructor->persona->user->removeRole('INSTRUCTOR');

        Log::info('Rol INSTRUCTOR removido del usuario al desasignar instructor de todas las fichas', [
            'user_id' => $instructor->persona->user->id,
            'persona_id' => $instructor->persona_id,
            'instructor_id' => $instructorId,
            'ficha_id' => $fichaId,
        ]);
    }
}
