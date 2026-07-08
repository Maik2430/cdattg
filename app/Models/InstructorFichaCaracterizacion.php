<?php

namespace App\Models;

use App\Models\Concerns\InstructorFichaCaracterizacion\CalculatesInstructorFichaProximaClase;
use App\Models\Concerns\InstructorFichaCaracterizacion\HasInstructorFichaCaracterizacionRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorFichaCaracterizacion extends Model
{
    use CalculatesInstructorFichaProximaClase;
    use HasFactory;
    use HasInstructorFichaCaracterizacionRelations;

    protected $table = 'instructor_fichas_caracterizacion';

    protected $fillable = [
        'instructor_id',
        'ficha_id',
        'competencia_id',
        'fecha_inicio',
        'fecha_fin',
        'total_horas_instructor',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];
}
