<?php

namespace App\Services\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorEliminacionActions
{
    /**
     * Elimina un instructor
     */
    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $instructor = Instructor::findOrFail($id);
            $personaId = $instructor->persona_id;

            $user = User::where('persona_id', $personaId)->first();
            if ($user && $user->hasRole('INSTRUCTOR')) {
                $user->removeRole('INSTRUCTOR');
            }

            $instructor->delete();

            Log::info('Instructor eliminado exitosamente', [
                'instructor_id' => $id,
            ]);

            return true;
        });
    }
}
