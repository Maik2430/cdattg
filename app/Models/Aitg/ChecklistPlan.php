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
        'descripcion_criterio',
        'orden',
    ];

    protected $casts = [
        'consecutivo' => 'integer',
        'orden' => 'integer',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PlanContratacion::class, 'plan_contratacion_id');
    }
}
