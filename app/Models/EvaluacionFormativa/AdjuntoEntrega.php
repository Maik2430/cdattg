<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdjuntoEntrega extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'adjuntos_entrega';

    protected $fillable = [
        'entrega_id',
        'nombre_original',
        'archivo_ruta',
        'mime_type',
        'tamano_bytes',
        'extension',
        'estado',
        'user_create_id',
        'user_edit_id',
        'user_delete_id',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'tamano_bytes' => 'integer',
    ];
}
