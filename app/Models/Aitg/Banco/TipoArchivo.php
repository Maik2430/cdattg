<?php

namespace App\Models\Aitg\Banco;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoArchivo extends Model
{
    protected $table = 'aitg_tipos_archivo';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'extensiones_permitidas',
        'tamano_max_kb',
        'es_obligatorio',
        'orden',
        'activo',
        'user_create_id',
        'user_update_id',
    ];

    protected $casts = [
        'extensiones_permitidas' => 'array',
        'es_obligatorio' => 'boolean',
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

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoBanco::class, 'tipo_archivo_id');
    }

    public function reglasMimes(): string
    {
        $exts = $this->extensiones_permitidas ?: ['pdf'];

        return implode(',', array_map(fn ($e) => strtolower(ltrim($e, '.')), $exts));
    }
}
