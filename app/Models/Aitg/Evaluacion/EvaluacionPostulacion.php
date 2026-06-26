<?php

namespace App\Models\Aitg\Evaluacion;

use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluacionPostulacion extends Model
{
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'en_evaluacion' => 'En evaluación',
        'requiere_subsanacion' => 'Requiere subsanación',
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado',
    ];

    protected $table = 'aitg_evaluaciones_postulacion';

    protected $fillable = [
        'postulacion_id',
        'estado',
        'puntaje_checklist',
        'puntaje_adicionales',
        'puntaje_total',
        'observaciones',
        'evaluador_user_id',
        'fecha_finalizacion',
    ];

    protected $casts = [
        'puntaje_checklist' => 'decimal:2',
        'puntaje_adicionales' => 'decimal:2',
        'puntaje_total' => 'decimal:2',
        'fecha_finalizacion' => 'datetime',
    ];

    public function postulacion(): BelongsTo
    {
        return $this->belongsTo(PostulacionPlan::class, 'postulacion_id');
    }

    public function evaluador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluador_user_id');
    }

    public function respuestasChecklist(): HasMany
    {
        return $this->hasMany(EvaluacionChecklistRespuesta::class, 'evaluacion_id');
    }

    public function respuestasPuntos(): HasMany
    {
        return $this->hasMany(EvaluacionPuntoRespuesta::class, 'evaluacion_id');
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function puedeEvaluar(): bool
    {
        return in_array($this->estado, ['pendiente', 'en_evaluacion', 'requiere_subsanacion'], true);
    }

    public function finalizada(): bool
    {
        return in_array($this->estado, ['aprobado', 'rechazado'], true);
    }
}
