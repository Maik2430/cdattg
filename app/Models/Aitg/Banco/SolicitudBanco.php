<?php

namespace App\Models\Aitg\Banco;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SolicitudBanco extends Model
{
    public const ESTADOS = [
        'borrador' => 'Borrador',
        'pendiente_revision' => 'Pendiente de revisión',
        'requiere_correccion' => 'Requiere corrección',
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado',
    ];

    protected $table = 'aitg_solicitudes_banco';

    protected $fillable = [
        'user_id',
        'persona_id',
        'estado',
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

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoBanco::class, 'solicitud_id');
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function puedeEditarDocumentos(): bool
    {
        return in_array($this->estado, ['borrador', 'requiere_correccion'], true);
    }
}
