<?php

namespace App\Models\Aitg;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistPlan extends Model
{
    protected $table = 'aitg_checklist_plan';

    protected $fillable = [
        'plan_contratacion_id',
        'consecutivo',
        'nombre',
        'descripcion_criterio',
        'puntaje',
        'es_obligatorio',
        'orden',
    ];

    protected $casts = [
        'consecutivo' => 'integer',
        'orden' => 'integer',
        'puntaje' => 'decimal:2',
        'es_obligatorio' => 'boolean',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PlanContratacion::class, 'plan_contratacion_id');
    }
}
