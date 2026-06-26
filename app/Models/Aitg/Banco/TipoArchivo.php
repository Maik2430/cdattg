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
        'categoria',
        'extensiones_permitidas',
        'tamano_max_kb',
        'es_obligatorio',
        'permite_multiples',
        'regla_visibilidad',
        'fase_carga',
        'orden',
        'activo',
        'user_create_id',
        'user_update_id',
    ];

    public const CATEGORIAS = [
        'obligatorios_base' => 'Documentos obligatorios base',
        'formacion_academica' => 'Formación académica',
        'experiencia' => 'Experiencia laboral y docente',
        'antecedentes' => 'Antecedentes',
        'requisitos_sena' => 'Requisitos SENA',
        'puntos_adicionales' => 'Puntos adicionales',
    ];

    public const REGLAS_VISIBILIDAD = [
        'siempre' => 'Siempre visible',
        'requiere_perfil' => 'Requiere perfil seleccionado',
        'requiere_exp_relacionada' => 'Si el perfil incluye experiencia relacionada',
        'requiere_exp_docente' => 'Si el perfil incluye experiencia docente',
    ];

    public const FASES_CARGA = [
        'inicial' => 'Postulación (validación inicial)',
        'post_seleccion' => 'Formalización y firma de contrato',
    ];

    protected $casts = [
        'extensiones_permitidas' => 'array',
        'es_obligatorio' => 'boolean',
        'permite_multiples' => 'boolean',
        'activo' => 'boolean',
    ];

    public function scopePorCategoria($query)
    {
        return $query->orderBy('categoria')->orderBy('orden');
    }

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
