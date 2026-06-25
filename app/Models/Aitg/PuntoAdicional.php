<?php

namespace App\Models\Aitg;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PuntoAdicional extends Model
{
    protected $table = 'aitg_puntos_adicionales';

    protected $fillable = [
        'plan_contratacion_id',
        'consecutivo',
        'descripcion',
        'puntaje_adicional',
        'orden',
    ];

    protected $casts = [
        'consecutivo' => 'integer',
        'puntaje_adicional' => 'decimal:2',
        'orden' => 'integer',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PlanContratacion::class, 'plan_contratacion_id');
    }
}
