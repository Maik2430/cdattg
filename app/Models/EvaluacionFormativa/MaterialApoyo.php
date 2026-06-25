<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialApoyo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'materiales_apoyo';

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo_material_id',
        'archivo_ruta',
        'archivo_url',
        'mime_type',
        'extension',
        'tamano_bytes',
        'estado',
        'user_create_id',
        'user_update_id',
        'user_delete_id',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'tamano_bytes' => 'integer',
    ];
}
