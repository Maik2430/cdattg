<?php

namespace App\Models\Aitg\Postulacion;

use App\Models\Aitg\Banco\PostulacionArchivo;
use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\ChecklistPlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostulacionChecklistItem extends Model
{
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'cargado' => 'Documento cargado',
        'pendiente_evaluacion' => 'Pendiente evaluación',
        'evaluado' => 'Evaluado',
        'requiere_subsanacion' => 'Requiere subsanación',
    ];

    protected $table = 'aitg_postulacion_checklist_items';

    protected $fillable = [
        'postulacion_id',
        'checklist_plan_id',
        'nombre',
        'descripcion_criterio',
        'puntaje',
        'es_obligatorio',
        'postulacion_archivo_id',
        'estado',
        'cumple',
        'observaciones',
        'solicita_actualizacion',
        'orden',
    ];

    protected $casts = [
        'puntaje' => 'decimal:2',
        'es_obligatorio' => 'boolean',
        'cumple' => 'boolean',
        'solicita_actualizacion' => 'boolean',
        'orden' => 'integer',
    ];

    public function postulacion(): BelongsTo
    {
        return $this->belongsTo(PostulacionPlan::class, 'postulacion_id');
    }

    public function checklistPlan(): BelongsTo
    {
        return $this->belongsTo(ChecklistPlan::class, 'checklist_plan_id');
    }

    public function postulacionArchivo(): BelongsTo
    {
        return $this->belongsTo(PostulacionArchivo::class, 'postulacion_archivo_id');
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function tieneDocumento(): bool
    {
        return $this->postulacion_archivo_id !== null;
    }

    public function puedeEvaluar(): bool
    {
        return in_array($this->estado, ['cargado', 'pendiente_evaluacion', 'evaluado', 'requiere_subsanacion'], true);
    }
}
