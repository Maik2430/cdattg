<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entrega extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'entregas';

    protected $fillable = [
        'item_id',
        'aprendiz_id',
        'juicio',
        'estado',
        'observacion_instructor',
        'user_create_id',
        'user_edit_id',
        'user_delete_id',
    ];

    protected $casts = [
        'juicio' => 'boolean',
    ];
}
