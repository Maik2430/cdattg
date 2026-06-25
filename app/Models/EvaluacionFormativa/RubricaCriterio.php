<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RubricaCriterio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rubricas_criterios';

    protected $fillable = [
        'rubrica_id',
        'criterio_id',
        'user_create_id',
        'user_edit_id',
        'user_delete_id',
    ];

    protected $casts = [];
}
