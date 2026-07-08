<?php

namespace App\Models\Concerns\PersonaIngresoSalida;

use App\Models\Ambiente;
use App\Models\FichaCaracterizacion;
use App\Models\Persona;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasPersonaIngresoSalidaRelations
{
    /**
     * Relación con Persona
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * Relación con Sede
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    /**
     * Relación con Ambiente
     */
    public function ambiente(): BelongsTo
    {
        return $this->belongsTo(Ambiente::class, 'ambiente_id');
    }

    /**
     * Relación con FichaCaracterizacion
     */
    public function fichaCaracterizacion(): BelongsTo
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_caracterizacion_id');
    }

    /**
     * Relación con User que creó el registro
     */
    public function userCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación con User que editó el registro
     */
    public function userUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }
}
