<?php

namespace App\Models\Concerns\Asistencia;

use App\Models\AsistenciaAprendiz;
use App\Models\Evidencias;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\User;

trait HasAsistenciaRelations
{
    /**
     * Relación con la evidencia
     * Una asistencia pertenece a una evidencia
     */
    public function evidencia()
    {
        return $this->belongsTo(Evidencias::class, 'evidencia_id');
    }

    /**
     * Relación con la ficha del instructor
     * Una asistencia pertenece a una ficha de instructor
     */
    public function instructorFicha()
    {
        return $this->belongsTo(InstructorFichaCaracterizacion::class, 'instructor_ficha_id');
    }

    /**
     * Relación con los aprendices de esta asistencia
     * Una asistencia tiene muchos registros de asistencia de aprendices
     */
    public function asistenciaAprendices()
    {
        return $this->hasMany(AsistenciaAprendiz::class, 'asistencia_id');
    }

    /**
     * Relación con el usuario que creó el registro
     */
    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación con el usuario que editó el registro
     */
    public function userEdit()
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }
}
