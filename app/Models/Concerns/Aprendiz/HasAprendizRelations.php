<?php

namespace App\Models\Concerns\Aprendiz;

use App\Models\AsistenciaAprendiz;
use App\Models\FichaCaracterizacion;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasAprendizRelations
{
    /**
     * Relación con Persona (Many-to-One).
     * Un aprendiz pertenece a una persona.
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * Relación con FichaCaracterizacion (Many-to-One).
     * Un aprendiz pertenece a una única ficha de caracterización.
     */
    public function fichaCaracterizacion(): BelongsTo
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_caracterizacion_id');
    }

    /**
     * Relación con AsistenciaAprendiz.
     * Un aprendiz tiene múltiples asistencias (aprendiz_ficha_id ahora apunta directamente a aprendices.id).
     */
    public function asistencias(): HasMany
    {
        return $this->hasMany(AsistenciaAprendiz::class, 'aprendiz_ficha_id', 'id');
    }

    /**
     * Relación con el usuario que creó el registro.
     */
    public function userCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación con el usuario que editó el registro por última vez.
     */
    public function userUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    /**
     * Obtiene el usuario asociado a través de la persona.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'persona_id', 'persona_id')
            ->through('persona');
    }
}
