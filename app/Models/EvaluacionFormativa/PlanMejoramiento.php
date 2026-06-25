<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanMejoramiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'planes_mejoramiento';

    protected $primaryKey = 'item_id';

    protected $fillable = [
        'aprendiz_id',
        'item_origen_id',
        'estado',
        'user_create_id',
        'user_edit_id',
        'user_delete_id',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];
}
