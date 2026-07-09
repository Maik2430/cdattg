<?php

namespace App\Models;

use App\Models\Concerns\Persona\BuildsPersonaAttributes;
use App\Models\Concerns\Persona\BuildsPersonaCaracterizacionAttributes;
use App\Models\Concerns\Persona\BuildsPersonaEstadoSofiaAttributes;
use App\Models\Concerns\Persona\ChecksPersonaRoles;
use App\Models\Concerns\Persona\SyncsPersonaLifecycle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property-read User|null $user
 * @property-read Parametro|null $tipoDocumento
 * @property-read Parametro|null $tipoGenero
 * @property-read Pais|null $pais
 * @property-read Departamento|null $departamento
 * @property-read Municipio|null $municipio
 * @property-read string $nombre_completo
 * @property-read Instructor|null $instructor
 * @property AsistenciaAprendiz|null $asistenciaHoy Atributo dinámico asignado en runtime (QR asistencia).
 * @property int|null $aprendiz_id Atributo dinámico asignado en runtime (QR asistencia).
 */
class Persona extends Model
{
    use BuildsPersonaAttributes, ChecksPersonaRoles, SyncsPersonaLifecycle;
    use BuildsPersonaCaracterizacionAttributes, BuildsPersonaEstadoSofiaAttributes;
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'fecha_nacimiento',
        'genero',
        'telefono',
        'celular',
        'email',
        'pais_id',
        'departamento_id',
        'municipio_id',
        'direccion',
        'status',
        'estado_sofia',
        'condocumento',
        'user_create_id',
        'user_edit_id',
        'parametro_id',
        'nivel_escolaridad_id',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'persona_id');
    }

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'tipo_documento');
    }

    public function tipoGenero(): BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'genero');
    }

    public function instructor(): HasOne
    {
        return $this->hasOne(Instructor::class);
    }

    public function proveedor(): HasOne
    {
        return $this->hasOne(\App\Models\Inventario\Proveedor::class);
    }

    public function caracterizacionProgramas(): HasMany
    {
        return $this->hasMany(FichaCaracterizacion::class, 'instructor_id');
    }

    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }

    /** Caracterización principal vía `parametro_id` (compatibilidad con vistas legacy). */
    public function caracterizacion(): BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'parametro_id');
    }

    public function aprendiz(): HasOne
    {
        return $this->hasOne(Aprendiz::class, 'persona_id');
    }

    public function estadoSofiaParametro(): BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'estado_sofia');
    }

    public function userCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function userUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    public function parametroCaracterizacion(): BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'parametro_id');
    }

    public function caracterizacionesComplementarias(): BelongsToMany
    {
        return $this->belongsToMany(Parametro::class, 'persona_caracterizacion', 'persona_id', 'parametro_id')
            ->withTimestamps();
    }

    public function contactAlerts(): HasMany
    {
        return $this->hasMany(PersonaContactAlert::class);
    }

    public function nivelEscolaridad(): BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'nivel_escolaridad_id');
    }
}
