<?php

namespace App\Models;

use App\Models\Concerns\Competencia\BuildsCompetenciaAttributes;
use App\Models\Concerns\Competencia\ChecksCompetenciaEstado;
use App\Models\Concerns\Competencia\HasCompetenciaRelations;
use App\Models\Concerns\Competencia\ScopesCompetencia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competencia extends Model
{
    use BuildsCompetenciaAttributes;
    use ChecksCompetenciaEstado;
    use HasCompetenciaRelations;
    use HasFactory;
    use ScopesCompetencia;

    protected $table = 'competencias';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'duracion',
        'fecha_inicio',
        'fecha_fin',
        'status',
        'user_create_id',
        'user_edit_id',
    ];

    protected $casts = [
        'duracion' => 'decimal:2',
        'status' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
