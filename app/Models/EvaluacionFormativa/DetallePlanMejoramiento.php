<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetallePlanMejoramiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detalles_plan_mejoramiento';

    protected $fillable = [
        'plan_mejoramiento_id',
        'criterios_deficientes',
        'user_create_id',
        'user_update_id',
        'user_delete_id',
    ];

    protected $casts = [];
}
