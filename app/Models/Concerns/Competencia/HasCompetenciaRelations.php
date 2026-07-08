<?php

namespace App\Models\Concerns\Competencia;

use App\Models\AsignacionInstructor;
use App\Models\ProgramaFormacion;
use App\Models\ResultadosAprendizaje;
use App\Models\ResultadosCompetencia;
use App\Models\User;

trait HasCompetenciaRelations
{
    public function resultadosCompetencia()
    {
        return $this->hasMany(ResultadosCompetencia::class);
    }

    public function resultadosAprendizaje()
    {
        return $this->belongsToMany(
            ResultadosAprendizaje::class,
            'resultados_aprendizaje_competencia',
            'competencia_id',
            'rap_id'
        )->withTimestamps()
            ->withPivot('duracion', 'user_create_id', 'user_edit_id');
    }

    public function asignacionesInstructor()
    {
        return $this->hasMany(AsignacionInstructor::class, 'competencia_id');
    }

    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function userEdit()
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    public function programasFormacion()
    {
        return $this->belongsToMany(
            ProgramaFormacion::class,
            'competencia_programa',
            'competencia_id',
            'programa_id'
        )->withTimestamps()
            ->withPivot('user_create_id', 'user_edit_id');
    }
}
