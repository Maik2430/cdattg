<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RapItemEvaluable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rap_item_evaluable';

    protected $fillable = [
        'item_evaluable_id',
        'rap_id',
        'estado',
        'user_create_id',
        'user_update_id',
        'user_delete_id',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];
}
