<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FichaItemEvaluable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fichas_item_evaluable';

    protected $fillable = [
        'ficha_id',
        'item_id',
        'estado',
        'user_create_id',
        'user_edit_id',
        'user_delete_id',
    ];

    protected $casts = [];
}
