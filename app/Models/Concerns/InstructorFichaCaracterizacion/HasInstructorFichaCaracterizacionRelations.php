<?php

namespace App\Models\Concerns\InstructorFichaCaracterizacion;

use App\Models\asistenciaAprendices;
use App\Models\Competencia;
use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use App\Models\InstructorFichaDias;
use App\Models\ResultadosAprendizaje;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasInstructorFichaCaracterizacionRelations
{
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    /**
     * Get the asistenciaAprendices that owns the InstructorFichaCaracterizacion
     */
    public function asistenciaAprendices(): BelongsTo
    {
        return $this->belongsTo(asistenciaAprendices::class, 'instructor_ficha_id');
    }

    public function ficha(): BelongsTo
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_id');
    }

    /**
     * Relación con Competencia
     */
    public function competencia(): BelongsTo
    {
        return $this->belongsTo(Competencia::class, 'competencia_id');
    }

    /**
     * Relación Many-to-Many con ResultadosAprendizaje
     */
    public function resultadosAprendizaje(): BelongsToMany
    {
        return $this->belongsToMany(
            ResultadosAprendizaje::class,
            'instructor_ficha_resultados_aprendizaje',
            'instructor_ficha_id',
            'resultado_aprendizaje_id'
        )->withTimestamps();
    }

    /**
     * Relación con los días de formación de la ficha a través de la tabla intermedia.
     */
    public function instructorFichaDias()
    {
        return $this->hasMany(InstructorFichaDias::class, 'instructor_ficha_id');
    }
}
