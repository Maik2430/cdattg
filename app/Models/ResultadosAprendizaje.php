<?php

namespace App\Models;

use App\Models\Concerns\ResultadosAprendizaje\BuildsResultadosAprendizajeAttributes;
use App\Models\Concerns\ResultadosAprendizaje\ChecksResultadosAprendizajeEstado;
use App\Models\Concerns\ResultadosAprendizaje\HasResultadosAprendizajeRelations;
use App\Models\Concerns\ResultadosAprendizaje\ScopesResultadosAprendizaje;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultadosAprendizaje extends Model
{
    use BuildsResultadosAprendizajeAttributes;

    use ChecksResultadosAprendizajeEstado;
    /** @use HasFactory<\Database\Factories\ResultadosAprendizajeFactory> */
    use HasFactory;
    use HasResultadosAprendizajeRelations;
    use ScopesResultadosAprendizaje;

    protected $table = 'resultados_aprendizajes';

    protected $fillable = [
        'codigo',
        'nombre',
        'duracion',
        'status',
        'user_create_id',
        'user_edit_id',
    ];

    protected $casts = [
        'duracion' => 'decimal:2',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
