<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaracterizacionPrograma extends Model
{
    protected $table = 'caracterizacion_programas';

    protected $fillable = [
        'ficha_id',
        'programa_formacion_id',
        'instructor_id',
        'instructor_persona_id',
        'jornada_id',
        'sede_id',
    ];

    public function ficha(): BelongsTo
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_id');
    }

    public function programaFormacion(): BelongsTo
    {
        return $this->belongsTo(ProgramaFormacion::class, 'programa_formacion_id');
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'instructor_persona_id', 'id');
    }

    public function jornada(): BelongsTo
    {
        // jornada_id apunta a parametros_temas (post migración drop jornadas_formacion)
        return $this->belongsTo(ParametroTema::class, 'jornada_id');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }
}
