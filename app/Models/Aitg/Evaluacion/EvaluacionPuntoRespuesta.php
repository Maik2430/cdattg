<?php

namespace App\Models\Aitg\Evaluacion;

use App\Models\Aitg\PuntoAdicional;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluacionPuntoRespuesta extends Model
{
    protected $table = 'aitg_evaluacion_puntos_respuestas';

    protected $fillable = [
        'evaluacion_id',
        'punto_adicional_id',
        'cumple',
        'observaciones',
    ];

    protected $casts = [
        'cumple' => 'boolean',
    ];

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(EvaluacionPostulacion::class, 'evaluacion_id');
    }

    public function puntoAdicional(): BelongsTo
    {
        return $this->belongsTo(PuntoAdicional::class, 'punto_adicional_id');
    }
}
