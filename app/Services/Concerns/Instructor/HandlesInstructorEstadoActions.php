<?php

namespace App\Services\Concerns\Instructor;

use App\Models\Instructor;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorEstadoActions
{
    /**
     * Cambia el estado de un instructor
     */
    public function cambiarEstado(int $id, bool $estado): bool
    {
        $instructor = Instructor::findOrFail($id);
        $instructor->update(['status' => $estado]);

        Log::info('Estado de instructor cambiado', [
            'instructor_id' => $id,
            'nuevo_estado' => $estado,
        ]);

        return true;
    }
}
