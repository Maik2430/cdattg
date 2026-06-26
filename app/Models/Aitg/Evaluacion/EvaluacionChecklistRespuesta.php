<?php

namespace App\Models\Aitg\Evaluacion;

use App\Models\Aitg\ChecklistPlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluacionChecklistRespuesta extends Model
{
    protected $table = 'aitg_evaluacion_checklist_respuestas';

    protected $fillable = [
        'evaluacion_id',
        'checklist_plan_id',
        'cumple',
        'observaciones',
        'solicita_actualizacion',
    ];

    protected $casts = [
        'cumple' => 'boolean',
        'solicita_actualizacion' => 'boolean',
    ];

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(EvaluacionPostulacion::class, 'evaluacion_id');
    }

    public function checklistPlan(): BelongsTo
    {
        return $this->belongsTo(ChecklistPlan::class, 'checklist_plan_id');
    }
}
