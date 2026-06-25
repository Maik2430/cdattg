<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rubrica extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rubricas';

    protected $fillable = [
        'nombre',
        'tipo_rubrica',
        'user_create_id',
        'user_edit_id',
        'user_delete_id',
    ];

    protected $casts = [];
}
