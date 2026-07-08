<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenciaGuiaAprendizaje extends Model
{
    use HasFactory;

    protected $table = 'evidencia_guia_aprendizaje';

    protected $fillable = ['evidencia_id', 'guia_aprendizaje_id', 'user_create_id', 'user_edit_id'];
}
