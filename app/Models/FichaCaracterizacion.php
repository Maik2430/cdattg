<?php

namespace App\Models;

use App\Models\Concerns\FichaCaracterizacion\BuildsFichaCaracterizacionResumen;
use App\Models\Concerns\FichaCaracterizacion\CalculatesFichaCaracterizacionDuracion;
use App\Models\Concerns\FichaCaracterizacion\HasFichaCaracterizacionAprendicesQueries;
use App\Models\Concerns\FichaCaracterizacion\HasFichaCaracterizacionEstadoTemporal;
use App\Models\Concerns\FichaCaracterizacion\ScopesFichaCaracterizacionEstado;
use App\Models\Concerns\FichaCaracterizacion\ScopesFichaCaracterizacionFechas;
use App\Models\Concerns\FichaCaracterizacion\ScopesFichaCaracterizacionFiltros;
use App\Models\Concerns\FichaCaracterizacion\SyncsFichaCaracterizacionInstructorLider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class FichaCaracterizacion extends Model
{
    use BuildsFichaCaracterizacionResumen;
    use CalculatesFichaCaracterizacionDuracion;
    use HasFactory;
    use HasFichaCaracterizacionAprendicesQueries;
    use HasFichaCaracterizacionEstadoTemporal;
    use ScopesFichaCaracterizacionEstado;
    use ScopesFichaCaracterizacionFechas;
    use ScopesFichaCaracterizacionFiltros;
    use SyncsFichaCaracterizacionInstructorLider;

    protected $table = 'fichas_caracterizacion';

    protected $fillable = [
        'programa_formacion_id',
        'ficha',
        'instructor_id',
        'fecha_inicio',
        'fecha_fin',
        'ambiente_id',
        'modalidad_formacion_id',
        'sede_id',
        'jornada_id',
        'total_horas',
        'user_create_id',
        'user_edit_id',
        'status',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'status' => 'boolean',
        'total_horas' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'fecha_inicio',
        'fecha_fin',
        'created_at',
        'updated_at',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function instructorFicha(): HasMany
    {
        return $this->hasMany(InstructorFichaCaracterizacion::class, 'ficha_id');
    }

    public function programaFormacion(): BelongsTo
    {
        return $this->belongsTo(ProgramaFormacion::class);
    }

    public function jornadaFormacion(): BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'jornada_id');
    }

    public function ambiente(): BelongsTo
    {
        return $this->belongsTo(Ambiente::class, 'ambiente_id');
    }

    public function modalidadFormacion(): BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'modalidad_formacion_id');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function regional(): HasOneThrough
    {
        return $this->hasOneThrough(
            Regional::class,
            Sede::class,
            'id',
            'id',
            'sede_id',
            'regional_id'
        );
    }

    public function usuarioCreacion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function usuarioEdicion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    public function diasFormacion(): HasMany
    {
        return $this->hasMany(FichaDiasFormacion::class, 'ficha_id', 'id');
    }

    public function instructorAsignado(): BelongsTo
    {
        return $this->belongsTo(InstructorFichaCaracterizacion::class, 'ficha_id');
    }

    public function aprendices(): HasMany
    {
        return $this->hasMany(Aprendiz::class, 'ficha_caracterizacion_id', 'id')
            ->where('aprendices.estado', 1);
    }

    public function aprendicesTodos(): HasMany
    {
        return $this->hasMany(Aprendiz::class, 'ficha_caracterizacion_id', 'id');
    }

    public function asignacionesInstructor(): HasMany
    {
        return $this->hasMany(AsignacionInstructor::class, 'ficha_id');
    }
}
