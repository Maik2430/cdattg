<?php

namespace App\Models;

use App\Models\Concerns\AsignacionInstructorLog\BuildsAsignacionInstructorLogAttributes;
use App\Models\Concerns\AsignacionInstructorLog\HasAsignacionInstructorLogRelations;
use App\Models\Concerns\AsignacionInstructorLog\ManagesAsignacionInstructorLogQueries;
use App\Models\Concerns\AsignacionInstructorLog\ScopesAsignacionInstructorLog;
use Illuminate\Database\Eloquent\Model;

class AsignacionInstructorLog extends Model
{
    use BuildsAsignacionInstructorLogAttributes;
    use HasAsignacionInstructorLogRelations;
    use ManagesAsignacionInstructorLogQueries;
    use ScopesAsignacionInstructorLog;

    protected $table = 'asignacion_instructor_logs';

    protected $fillable = [
        'instructor_id',
        'ficha_id',
        'accion',
        'detalles',
        'resultado',
        'mensaje',
        'user_id',
        'fecha_accion',
        'datos_anteriores',
        'datos_nuevos',
    ];

    protected $casts = [
        'detalles' => 'array',
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'fecha_accion' => 'datetime',
    ];
}
