<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RedConocimiento extends Model
{
    use HasFactory;

    protected $table = 'red_conocimientos';

    protected $fillable = [
        'nombre',
        'regionals_id',
        'user_create_id',
        'user_edit_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Boot del modelo para eventos.
     */
    protected static function boot()
    {
        parent::boot();

        // Convertir el nombre a mayúsculas antes de guardar
        static::saving(function ($redConocimiento) {
            $redConocimiento->nombre = strtoupper($redConocimiento->nombre);
        });
    }

    /**
     * Relación: Red de conocimiento pertenece a una regional.
     */
    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class, 'regionals_id');
    }

    /**
     * Relación: Red de conocimiento creada por un usuario.
     */
    public function userCreated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación: Red de conocimiento modificada por un usuario.
     */
    public function userEdited(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    /**
     * Relación: Red de conocimiento tiene muchos programas de formación.
     */
    public function programasFormacion(): HasMany
    {
        return $this->hasMany(ProgramaFormacion::class, 'red_conocimiento_id');
    }

    // Scopes para filtros comunes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function scopeByRegional($query, $regionalId)
    {
        return $query->where('regionals_id', $regionalId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nombre', 'LIKE', "%{$search}%")
              ->orWhereHas('regional', function ($subQuery) use ($search) {
                  $subQuery->where('nombre', 'LIKE', "%{$search}%");
              });
        });
    }
}
