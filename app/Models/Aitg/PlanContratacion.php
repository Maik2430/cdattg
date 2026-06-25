<?php

namespace App\Models\Aitg;

use App\Models\Competencia;
use App\Models\Regional;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanContratacion extends Model
{
    public const MODALIDADES = [
        'regular' => 'Regular',
        'virtual' => 'Virtual',
        'fic' => 'FIC',
    ];

    public const ESTADOS = [
        'borrador' => 'Borrador',
        'activo' => 'Activo',
        'cerrado' => 'Cerrado',
    ];

    public const TIPOS_REGISTRO_PERFIL = [
        'opcion' => 'Por opción',
        'alternativa' => 'Por alternativa',
        'directo' => 'Por nivel de formación y programa',
    ];

    protected $table = 'aitg_planes_contratacion';

    protected $fillable = [
        'competencia_id',
        'tipo_registro_perfil',
        'modalidad',
        'regional_id',
        'periodo',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'tope_global',
        'observaciones',
        'user_create_id',
        'user_update_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'tope_global' => 'decimal:2',
    ];

    public function competencia(): BelongsTo
    {
        return $this->belongsTo(Competencia::class, 'competencia_id');
    }

    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class);
    }

    public function perfiles(): HasMany
    {
        return $this->hasMany(PerfilPlan::class, 'plan_contratacion_id')->orderBy('consecutivo');
    }

    public function checklist(): HasMany
    {
        return $this->hasMany(ChecklistPlan::class, 'plan_contratacion_id')->orderBy('consecutivo');
    }

    public function puntosAdicionales(): HasMany
    {
        return $this->hasMany(PuntoAdicional::class, 'plan_contratacion_id')->orderBy('consecutivo');
    }

    public function userCreated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function userUpdated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_update_id');
    }

    public function getModalidadLabelAttribute(): string
    {
        return self::MODALIDADES[$this->modalidad] ?? $this->modalidad;
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getTipoRegistroPerfilLabelAttribute(): string
    {
        return self::TIPOS_REGISTRO_PERFIL[$this->tipo_registro_perfil] ?? $this->tipo_registro_perfil;
    }
}
