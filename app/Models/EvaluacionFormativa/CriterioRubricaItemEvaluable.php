<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CriterioRubricaItemEvaluable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'criterios_rubricas_item_evaluable';

    protected $fillable = [
        'item_evaluable_id',
        'rubricas_criterios_id',
        'peso_porcentual',
        'user_create_id',
        'user_edit_id',
        'user_delete_id',
    ];

    protected $casts = [
        'peso_porcentual' => 'float',
    ];
}
