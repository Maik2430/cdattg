<?php

namespace App\Models;

use App\Models\Concerns\PersonaIngresoSalida\HasPersonaIngresoSalidaRelations;
use App\Models\Concerns\PersonaIngresoSalida\ManagesPersonaIngresoSalidaPresencia;
use App\Models\Concerns\PersonaIngresoSalida\ResolvesPersonaIngresoSalidaTipos;
use App\Models\Concerns\PersonaIngresoSalida\ScopesPersonaIngresoSalida;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonaIngresoSalida extends Model
{
    use HasFactory;
    use HasPersonaIngresoSalidaRelations;
    use ManagesPersonaIngresoSalidaPresencia;
    use ResolvesPersonaIngresoSalidaTipos;
    use ScopesPersonaIngresoSalida;

    protected $table = 'persona_ingreso_salida';

    protected $fillable = [
        'persona_id',
        'sede_id',
        'tipo_persona',
        'fecha_entrada',
        'hora_entrada',
        'timestamp_entrada',
        'fecha_salida',
        'hora_salida',
        'timestamp_salida',
        'ambiente_id',
        'ficha_caracterizacion_id',
        'observaciones',
        'user_create_id',
        'user_edit_id',
    ];

    protected $casts = [
        'fecha_entrada' => 'date',
        'fecha_salida' => 'date',
        'timestamp_entrada' => 'datetime',
        'timestamp_salida' => 'datetime',
    ];
}
