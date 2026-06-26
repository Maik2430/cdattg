<?php

namespace App\Models\Aitg\Banco;

use App\Models\Competencia;
use App\Models\Aitg\Convocatoria\Convocatoria;
use App\Models\Aitg\Evaluacion\EvaluacionPostulacion;
use App\Models\Aitg\PerfilPlan;
use App\Models\Aitg\PlanContratacion;
use App\Models\Aitg\Postulacion\PostulacionChecklistItem;
use App\Models\Aitg\Postulacion\PostulacionPuntoItem;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostulacionPlan extends Model
{
    public const ESTADOS = [
        'borrador' => 'Borrador',
        'pendiente_revision' => 'Pendiente de revisión',
        'requiere_correccion' => 'Requiere corrección',
        'preseleccionado' => 'Pendiente evaluación',
        'evaluacion_aprobada' => 'Evaluación aprobada',
        'seleccionado' => 'Seleccionado',
        'suplente' => 'Suplente',
        'aprobado' => 'Habilitado en Banco de Talento',
        'rechazado' => 'Rechazado',
    ];

    public const ESTADOS_EVALUACION = [
        'preseleccionado',
        'evaluacion_aprobada',
        'seleccionado',
        'suplente',
    ];

    protected $table = 'aitg_postulaciones_plan';

    protected $fillable = [
        'user_id',
        'persona_id',
        'competencia_id',
        'plan_contratacion_id',
        'convocatoria_id',
        'perfil_plan_id',
        'estado',
        'fase_actual',
        'fecha_envio',
        'fecha_resolucion',
        'observaciones_validador',
        'user_create_id',
        'user_update_id',
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'fecha_resolucion' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function competencia(): BelongsTo
    {
        return $this->belongsTo(Competencia::class, 'competencia_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PlanContratacion::class, 'plan_contratacion_id');
    }

    public function convocatoria(): BelongsTo
    {
        return $this->belongsTo(Convocatoria::class, 'convocatoria_id');
    }

    public function perfilPlan(): BelongsTo
    {
        return $this->belongsTo(PerfilPlan::class, 'perfil_plan_id');
    }

    public function archivos(): HasMany
    {
        return $this->hasMany(PostulacionArchivo::class, 'postulacion_id');
    }

    public function evaluacion(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EvaluacionPostulacion::class, 'postulacion_id');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(PostulacionChecklistItem::class, 'postulacion_id');
    }

    public function puntoItems(): HasMany
    {
        return $this->hasMany(PostulacionPuntoItem::class, 'postulacion_id');
    }

    public function esBancoTalento(): bool
    {
        return $this->convocatoria_id === null;
    }

    public function esConvocatoria(): bool
    {
        return $this->convocatoria_id !== null;
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function puedeEditar(): bool
    {
        if ($this->esEnFormalizacion()) {
            return in_array($this->estado, ['seleccionado', 'requiere_correccion'], true);
        }

        return in_array($this->estado, ['borrador', 'requiere_correccion'], true);
    }

    public function esEnFormalizacion(): bool
    {
        return $this->esConvocatoria()
            && $this->fase_actual === 'post_seleccion'
            && in_array($this->estado, ['seleccionado', 'requiere_correccion', 'pendiente_revision'], true);
    }

    public function puedeCargarFormalizacion(): bool
    {
        return $this->esConvocatoria()
            && $this->fase_actual === 'post_seleccion'
            && $this->estado === 'seleccionado';
    }

    public function faseDocumental(): string
    {
        if ($this->fase_actual === 'post_seleccion') {
            return 'post_seleccion';
        }

        return 'inicial';
    }

    public function faseDocumentalLabel(): string
    {
        if ($this->faseDocumental() === 'post_seleccion') {
            return 'Documentos de formalización y firma de contrato';
        }

        if ($this->esBancoTalento()) {
            return 'Documentos de postulación — validación inicial (Banco de Talento)';
        }

        return 'Documentos de postulación — validación inicial';
    }

    public function requierePerfil(): bool
    {
        return $this->esConvocatoria() && $this->perfil_plan_id === null;
    }

    public function nombreCompetencia(): string
    {
        return $this->competencia?->nombre
            ?? $this->plan?->competencia?->nombre
            ?? 'Competencia #'.($this->competencia_id ?? '?');
    }

    /** Estados del banco en los que el aspirante puede retirar/eliminar su postulación. */
    public const ESTADOS_ELIMINABLES_BANCO = [
        'borrador',
        'pendiente_revision',
        'requiere_correccion',
        'rechazado',
    ];

    public function puedeEliminar(): bool
    {
        if ($this->esBancoTalento()) {
            return in_array($this->estado, self::ESTADOS_ELIMINABLES_BANCO, true);
        }

        return in_array($this->estado, ['borrador', 'rechazado'], true);
    }

    public function mensajeConfirmacionEliminar(): string
    {
        if ($this->esBancoTalento() && $this->estado === 'pendiente_revision') {
            return '¿Retirar su postulación del Banco de Talento? Se eliminarán los documentos enviados a revisión y podrá volver a inscribirse.';
        }

        if ($this->esBancoTalento() && $this->estado === 'requiere_correccion') {
            return '¿Eliminar su postulación? Se perderán los documentos cargados y las observaciones del validador. Podrá iniciar una postulación nueva.';
        }

        if ($this->esBancoTalento()) {
            return '¿Eliminar su postulación en el Banco de Talento? Podrá volver a inscribirse en esta competencia cuando lo desee.';
        }

        return '¿Eliminar su postulación? Podrá volver a postular si la convocatoria sigue abierta.';
    }

    public function mensajeNoEliminable(): ?string
    {
        if (! $this->esBancoTalento() || $this->puedeEliminar()) {
            return null;
        }

        if ($this->estado === 'aprobado') {
            return 'Su acreditación en el Banco de Talento está activa. No puede eliminar esta postulación; ya puede postular en convocatorias de esta competencia.';
        }

        return 'Esta postulación no puede eliminarse en su estado actual.';
    }

    public function bancoHabilitado(): bool
    {
        return $this->esBancoTalento() && $this->estado === 'aprobado';
    }
}
