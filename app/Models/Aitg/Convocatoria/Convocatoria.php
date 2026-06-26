<?php

namespace App\Models\Aitg\Convocatoria;

use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\PlanContratacion;
use App\Models\CentroFormacion;
use App\Models\Competencia;
use App\Models\ProgramaFormacion;
use App\Models\Regional;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Convocatoria extends Model
{
    public const ESTADOS = [
        'borrador' => 'Borrador',
        'publicada' => 'Publicada',
        'cerrada' => 'Cerrada',
        'finalizada' => 'Finalizada',
    ];

    public const ESTADOS_MANUALES = ['borrador', 'publicada'];

    protected $table = 'aitg_convocatorias';

    protected $fillable = [
        'codigo',
        'titulo',
        'competencia_id',
        'plan_contratacion_id',
        'descripcion',
        'objeto_contractual',
        'requisitos',
        'estado',
        'postulacion_seleccionada_id',
        'codigo_cdp',
        'valor_total',
        'valor_contrato_honorarios',
        'fecha_inicio_publicacion',
        'fecha_fin_publicacion',
        'fecha_inicio_contrato',
        'fecha_fin_contrato',
        'centro_formacion_id',
        'regional_id',
        'programa_formacion_id',
        'fecha_publicacion',
        'user_create_id',
        'user_update_id',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'valor_contrato_honorarios' => 'decimal:2',
        'fecha_inicio_publicacion' => 'date',
        'fecha_fin_publicacion' => 'date',
        'fecha_inicio_contrato' => 'date',
        'fecha_fin_contrato' => 'date',
        'fecha_publicacion' => 'datetime',
    ];

    public function competencia(): BelongsTo
    {
        return $this->belongsTo(Competencia::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PlanContratacion::class, 'plan_contratacion_id');
    }

    public function centroFormacion(): BelongsTo
    {
        return $this->belongsTo(CentroFormacion::class, 'centro_formacion_id');
    }

    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class);
    }

    public function programaFormacion(): BelongsTo
    {
        return $this->belongsTo(ProgramaFormacion::class, 'programa_formacion_id');
    }

    public function postulaciones(): HasMany
    {
        return $this->hasMany(PostulacionPlan::class, 'convocatoria_id');
    }

    public function postulacionSeleccionada(): BelongsTo
    {
        return $this->belongsTo(PostulacionPlan::class, 'postulacion_seleccionada_id');
    }

    public function userCreated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function userUpdated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_update_id');
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function badgeClassEstado(): string
    {
        return match ($this->estado) {
            'publicada' => 'success',
            'cerrada' => 'warning',
            'finalizada' => 'info',
            'borrador' => 'secondary',
            default => 'light',
        };
    }

    public function esVisiblePara(?User $user): bool
    {
        if ($this->estado === 'borrador') {
            return $user?->can('VER CONVOCATORIA AITG') ?? false;
        }

        return in_array($this->estado, ['publicada', 'cerrada', 'finalizada'], true);
    }

    /** @deprecated Use esVisiblePara */
    public function esVisibleAspirante(): bool
    {
        return in_array($this->estado, ['publicada', 'cerrada', 'finalizada'], true);
    }

    public function puedePostular(): bool
    {
        if ($this->estado !== 'publicada') {
            return false;
        }

        $hoy = now()->startOfDay();

        if ($this->fecha_inicio_publicacion && $hoy->lt($this->fecha_inicio_publicacion)) {
            return false;
        }

        if ($this->fecha_fin_publicacion && $hoy->gt($this->fecha_fin_publicacion)) {
            return false;
        }

        return true;
    }

    public function soloLectura(): bool
    {
        return in_array($this->estado, ['cerrada', 'finalizada'], true);
    }

    public function postulacionesCount(): int
    {
        return $this->postulaciones()->count();
    }
}
