<?php

namespace App\Models\Aitg\Banco;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MotivoRechazo extends Model
{
    protected $table = 'aitg_motivos_rechazo';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
        'orden',
        'user_create_id',
        'user_update_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true)->orderBy('orden');
    }

    public function userCreated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function userUpdated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_update_id');
    }

    public function validaciones(): HasMany
    {
        return $this->hasMany(ValidacionDocumento::class, 'motivo_rechazo_id');
    }
}
