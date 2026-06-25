<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Criterio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'criterios';

    protected $fillable = [
        'nombre',
        'user_create_id',
        'user_update_id',
        'user_delete_id',
    ];

    protected $casts = [];
}
