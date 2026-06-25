<?php

namespace App\Models\Aitg;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfilPlan extends Model
{
    protected $table = 'aitg_perfiles_plan';

    protected $fillable = [
        'plan_contratacion_id',
        'consecutivo',
        'descripcion_criterio',
        'descripcion_criterio_programa',
        'incluye_experiencia',
        'experiencia_relacionada_meses',
        'experiencia_docencia_meses',
    ];

    protected $casts = [
        'consecutivo' => 'integer',
        'incluye_experiencia' => 'boolean',
        'experiencia_relacionada_meses' => 'integer',
        'experiencia_docencia_meses' => 'integer',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PlanContratacion::class, 'plan_contratacion_id');
    }
}
