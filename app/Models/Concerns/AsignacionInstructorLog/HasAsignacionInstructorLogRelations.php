<?php

namespace App\Models\Concerns\AsignacionInstructorLog;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasAsignacionInstructorLogRelations
{
    /**
     * Relación con Instructor
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * Relación con FichaCaracterizacion
     */
    public function ficha(): BelongsTo
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_id');
    }

    /**
     * Relación con User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
