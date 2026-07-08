<?php

namespace App\Models;

use App\Models\Concerns\Instructor\BuildsInstructorFormattedAttributes;
use App\Models\Concerns\Instructor\BuildsInstructorPersonaAttributes;
use App\Models\Concerns\Instructor\HasInstructorFichasQueries;
use App\Models\Concerns\Instructor\ManagesInstructorEspecialidadesCompetencias;
use App\Models\Concerns\Instructor\ScopesInstructorEspecialidadCompetencia;
use App\Models\Concerns\Instructor\ScopesInstructorEstado;
use App\Models\Concerns\Instructor\ScopesInstructorFiltros;
use App\Models\Concerns\Instructor\SyncsInstructorCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read Regional|null $regional
 */
class Instructor extends Model
{
    use BuildsInstructorFormattedAttributes;
    use BuildsInstructorPersonaAttributes;
    use HasFactory;
    use HasInstructorFichasQueries;
    use ManagesInstructorEspecialidadesCompetencias;
    use ScopesInstructorEspecialidadCompetencia;
    use ScopesInstructorEstado;
    use ScopesInstructorFiltros;
    use SyncsInstructorCache;

    protected $table = 'instructors';

    protected $fillable = [
        'persona_id', 'regional_id', 'status', 'user_create_id', 'user_edit_id',
        'especialidades', 'competencias', 'anos_experiencia', 'experiencia_laboral',
        'numero_documento_cache', 'nombre_completo_cache', 'tipo_vinculacion_id', 'jornadas',
        'centro_formacion_id', 'experiencia_instructor_meses', 'fecha_ingreso_sena',
        'nivel_academico_id', 'titulos_obtenidos', 'instituciones_educativas',
        'certificaciones_tecnicas', 'cursos_complementarios', 'formacion_pedagogia',
        'areas_experticia', 'competencias_tic', 'idiomas', 'habilidades_pedagogicas',
        'documentos_adjuntos', 'numero_contrato', 'fecha_inicio_contrato', 'fecha_fin_contrato',
        'supervisor_contrato', 'eps', 'arl',
    ];

    protected $casts = [
        'status' => 'boolean',
        'jornadas' => 'array',
        'especialidades' => 'array',
        'competencias' => 'array',
        'anos_experiencia' => 'integer',
        'experiencia_instructor_meses' => 'integer',
        'fecha_ingreso_sena' => 'date',
        'fecha_inicio_contrato' => 'date',
        'fecha_fin_contrato' => 'date',
        'titulos_obtenidos' => 'array',
        'instituciones_educativas' => 'array',
        'certificaciones_tecnicas' => 'array',
        'cursos_complementarios' => 'array',
        'areas_experticia' => 'array',
        'competencias_tic' => 'array',
        'idiomas' => 'array',
        'habilidades_pedagogicas' => 'array',
        'documentos_adjuntos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class, 'regional_id');
    }

    public function centroFormacion(): BelongsTo
    {
        return $this->belongsTo(CentroFormacion::class, 'centro_formacion_id');
    }

    public function tipoVinculacion(): BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'tipo_vinculacion_id');
    }

    public function jornadas(): BelongsToMany
    {
        return $this->belongsToMany(ParametroTema::class, 'instructor_parametro_tema', 'instructor_id', 'parametro_tema_id')
            ->whereHas('tema', fn ($q) => $q->where('name', 'LIKE', '%JORNADAS%'))
            ->withPivot('user_create_id', 'user_edit_id')
            ->withTimestamps();
    }

    public function modalidades(): BelongsToMany
    {
        return $this->belongsToMany(ParametroTema::class, 'instructor_parametro_tema', 'instructor_id', 'parametro_tema_id')
            ->whereHas('tema', fn ($q) => $q->where('id', 5))
            ->withPivot('user_create_id', 'user_edit_id')
            ->withTimestamps();
    }

    public function nivelAcademico(): BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'nivel_academico_id');
    }

    public function fichas(): HasMany
    {
        return $this->hasMany(FichaCaracterizacion::class, 'instructor_id');
    }

    public function instructorFichas(): HasMany
    {
        return $this->hasMany(InstructorFichaCaracterizacion::class, 'instructor_id');
    }

    public function asignacionesInstructor(): HasMany
    {
        return $this->hasMany(AsignacionInstructor::class, 'instructor_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'persona_id', 'persona_id');
    }

    public function userCreated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function userEdited(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    public function entradaSalidas(): HasMany
    {
        return $this->hasMany(EntradaSalida::class, 'instructor_user_id', 'persona_id');
    }
}
