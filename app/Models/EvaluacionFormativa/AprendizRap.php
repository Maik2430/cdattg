<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AprendizRap extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'aprendices_raps';

    protected $fillable = [
        'aprendiz_id',
        'rap_id',
        'estado_id',
        'user_create_id',
        'user_update_id',
        'user_delete_id',
    ];

    protected $casts = [];
}
