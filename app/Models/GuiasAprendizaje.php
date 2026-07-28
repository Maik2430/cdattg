<?php

namespace App\Models;

use App\Models\Concerns\GuiasAprendizaje\BuildsGuiasAprendizajeFormattedAttributes;
use App\Models\Concerns\GuiasAprendizaje\BuildsGuiasAprendizajeResumen;
use App\Models\Concerns\GuiasAprendizaje\CalculatesGuiasAprendizajeDuracion;
use App\Models\Concerns\GuiasAprendizaje\ChecksGuiasAprendizajeEstado;
use App\Models\Concerns\GuiasAprendizaje\CountsGuiasAprendizajeAsociaciones;
use App\Models\Concerns\GuiasAprendizaje\ScopesGuiasAprendizajeEstado;
use App\Models\Concerns\GuiasAprendizaje\ScopesGuiasAprendizajeFiltros;
use App\Models\Concerns\GuiasAprendizaje\ScopesGuiasAprendizajeOrdenamiento;
use App\Models\Concerns\GuiasAprendizaje\SyncsGuiasAprendizajeLifecycle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuiasAprendizaje extends Model
{
    use BuildsGuiasAprendizajeFormattedAttributes;
    use BuildsGuiasAprendizajeResumen;
    use CalculatesGuiasAprendizajeDuracion;
    use ChecksGuiasAprendizajeEstado;
    use CountsGuiasAprendizajeAsociaciones;
    use HasFactory;
    use ScopesGuiasAprendizajeEstado;
    use ScopesGuiasAprendizajeFiltros;
    use ScopesGuiasAprendizajeOrdenamiento;
    use SoftDeletes;
    use SyncsGuiasAprendizajeLifecycle;

    protected $table = 'guia_aprendizajes';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'programa_formacion_id',
        'duracion_horas',
        'duracion_meses',
        'objetivo_general',
        'metodologia',
        'evaluacion',
        'status',
        'user_create_id',
        'user_edit_id',
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function userCreate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function userEdit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    public function programaFormacion(): BelongsTo
    {
        return $this->belongsTo(ProgramaFormacion::class, 'programa_formacion_id');
    }

    public function resultadosAprendizaje(): BelongsToMany
    {
        return $this->belongsToMany(ResultadosAprendizaje::class, 'guia_aprendizaje_rap', 'guia_aprendizaje_id', 'rap_id')
            ->withPivot('user_create_id', 'user_edit_id', 'es_obligatorio')
            ->withTimestamps();
    }

    public function guiaAprendizajeRap(): HasMany
    {
        return $this->hasMany(GuiaAprendizajeRap::class, 'guia_aprendizaje_id');
    }

    public function actividades(): BelongsToMany
    {
        $relacion = $this->belongsToMany(Evidencias::class, 'evidencia_guia_aprendizaje', 'guia_aprendizaje_id', 'evidencia_id')
            ->withPivot('user_create_id', 'user_edit_id')
            ->withTimestamps();

        // FIELD() es MySQL; en SQLite usamos CASE compatible.
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite') {
            return $relacion
                ->orderByRaw("CASE id_estado WHEN '25' THEN 0 WHEN '27' THEN 1 ELSE 2 END")
                ->orderBy('fecha_evidencia', 'asc');
        }

        return $relacion
            ->orderByRaw("FIELD(id_estado, '25', '27')")
            ->orderBy('fecha_evidencia', 'asc');
    }

    public function evidencias(): BelongsToMany
    {
        return $this->belongsToMany(Evidencias::class, 'evidencia_guia_aprendizaje', 'guia_aprendizaje_id', 'evidencia_id')
            ->withPivot('user_create_id', 'user_edit_id')
            ->withTimestamps();
    }
}
