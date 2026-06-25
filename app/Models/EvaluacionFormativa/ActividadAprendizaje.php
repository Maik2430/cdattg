<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActividadAprendizaje extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'actividades_aprendizaje';

    protected $primaryKey = 'item_id';

    protected $fillable = [
        'tipo_actividad',
        'user_create_id',
        'user_edit_id',
        'user_delete_id',
    ];

    protected $casts = [];
}
