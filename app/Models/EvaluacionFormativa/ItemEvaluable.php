<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemEvaluable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'item_evaluable';

    protected $primaryKey = 'item_id';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_limite_entrega',
        'estado',
        'tipo_actividad',
        'user_create_id',
        'user_edit_id',
        'user_delete_id',
    ];

    protected $casts = [
        'fecha_limite_entrega' => 'datetime',
    ];
}
