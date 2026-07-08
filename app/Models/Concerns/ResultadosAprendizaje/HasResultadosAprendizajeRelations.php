<?php

namespace App\Models\Concerns\ResultadosAprendizaje;

use App\Models\AsignacionInstructor;
use App\Models\Competencia;
use App\Models\GuiaAprendizajeRap;
use App\Models\GuiasAprendizaje;
use App\Models\ResultadosCompetencia;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasResultadosAprendizajeRelations
{
    /**
     * Relación muchos a muchos con GuiasAprendizaje a través de la tabla intermedia
     */
    public function guiasAprendizaje()
    {
        return $this->belongsToMany(GuiasAprendizaje::class, 'guia_aprendizaje_rap', 'rap_id', 'guia_aprendizaje_id')
            ->withPivot('user_create_id', 'user_edit_id')
            ->withTimestamps();
    }

    /**
     * Relación con la tabla intermedia
     */
    public function guiaAprendizajeRap()
    {
        return $this->hasMany(GuiaAprendizajeRap::class, 'rap_id');
    }

    /**
     * Relación muchos a muchos con Competencia a través de la tabla intermedia
     *
     * @return BelongsToMany<Competencia, $this>
     */
    public function competencias(): BelongsToMany
    {
        return $this->belongsToMany(Competencia::class, 'resultados_aprendizaje_competencia', 'rap_id', 'competencia_id')
            ->withPivot('duracion', 'user_create_id', 'user_edit_id')
            ->withTimestamps();
    }

    public function asignacionesInstructor()
    {
        return $this->belongsToMany(
            AsignacionInstructor::class,
            'asignacion_instructor_resultado',
            'resultado_aprendizaje_id',
            'asignacion_id'
        )->withTimestamps();
    }

    /**
     * Relación con la tabla intermedia resultados_aprendizaje_competencia
     */
    public function resultadosCompetencia()
    {
        return $this->hasMany(ResultadosCompetencia::class, 'rap_id');
    }

    /**
     * Relación con el usuario que creó el resultado
     */
    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación con el usuario que editó el resultado
     */
    public function userEdit()
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }
}
