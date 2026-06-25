<?php

namespace App\Models\EvaluacionFormativa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemMaterialApoyo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'item_materiales_apoyo';

    protected $fillable = [
        'item_id',
        'material_apoyo_id',
        'user_create_id',
        'user_edit_id',
        'user_delete_id',
    ];

    protected $casts = [];
}
