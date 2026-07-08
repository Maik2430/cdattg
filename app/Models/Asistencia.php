<?php

namespace App\Models;

use App\Models\Concerns\Asistencia\BuildsAsistenciaAttributes;
use App\Models\Concerns\Asistencia\HasAsistenciaRelations;
use App\Models\Concerns\Asistencia\ManagesAsistenciaCreacion;
use App\Models\Concerns\Asistencia\ManagesAsistenciaEstado;
use App\Models\Concerns\Asistencia\ScopesAsistencia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use BuildsAsistenciaAttributes;
    use HasAsistenciaRelations;
    use HasFactory;
    use ManagesAsistenciaCreacion;
    use ManagesAsistenciaEstado;
    use ScopesAsistencia;

    protected $table = 'asistencias';

    protected $fillable = [
        'evidencia_id',
        'instructor_ficha_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'is_finished',
        'user_create_id',
        'user_edit_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_inicio' => 'datetime',
        'hora_fin' => 'datetime',
        'is_finished' => 'boolean',
    ];

    protected $dates = [
        'fecha',
        'hora_inicio',
        'hora_fin',
    ];
}
