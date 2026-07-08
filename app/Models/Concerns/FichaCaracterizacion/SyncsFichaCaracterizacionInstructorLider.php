<?php

namespace App\Models\Concerns\FichaCaracterizacion;

use App\Models\InstructorFichaCaracterizacion;

trait SyncsFichaCaracterizacionInstructorLider
{
    /**
     * Asegura que exista un registro en instructor_fichas_caracterizacion para el
     * instructor líder (instructor_id) de esta ficha, para que "Tomar asistencia"
     * muestre la ficha al instructor.
     */
    public function syncInstructorLiderToPivot(): void
    {
        if (! $this->instructor_id) {
            return;
        }

        $pivot = InstructorFichaCaracterizacion::firstOrNew([
            'instructor_id' => $this->instructor_id,
            'ficha_id' => $this->id,
        ]);

        $pivot->fecha_inicio = $this->fecha_inicio;
        $pivot->fecha_fin = $this->fecha_fin;
        $pivot->total_horas_instructor = $this->total_horas ?? 0;
        $pivot->save();
    }
}
