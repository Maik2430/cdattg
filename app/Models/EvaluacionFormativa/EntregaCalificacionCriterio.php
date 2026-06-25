<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EntregaCalificacionCriterio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'entregas_calificaciones_criterios';

    protected $fillable = [
        'entrega_id',
        'rubrica_criterio_id',
        'juicio',
        'estado',
        'user_create_id',
        'user_update_id',
        'user_delete_id',
    ];

    protected $casts = [
        'juicio' => 'boolean',
        'estado' => 'boolean',
    ];
}
