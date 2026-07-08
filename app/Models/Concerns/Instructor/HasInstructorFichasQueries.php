<?php

namespace App\Models\Concerns\Instructor;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasInstructorFichasQueries
{
    /**
     * Calcular el total de horas asignadas al instructor.
     */
    public function getTotalHorasAsignadasAttribute(): int
    {
        return $this->instructorFichas()->sum('total_horas_instructor') ?? 0;
    }

    /**
     * Obtener el número de fichas asignadas.
     */
    public function getNumeroFichasAsignadasAttribute(): int
    {
        return $this->fichas()->count();
    }

    /**
     * Verificar si el instructor tiene fichas activas.
     */
    public function tieneFichasActivas(): bool
    {
        return $this->fichas()->where('status', true)->exists();
    }

    /**
     * Obtener las fichas activas del instructor.
     */
    public function fichasActivas(): HasMany
    {
        return $this->fichas()->where('status', true);
    }

    /**
     * Verificar si el instructor está disponible para nuevas asignaciones.
     */
    public function estaDisponible(): bool
    {
        return $this->status && ! $this->tieneFichasActivas();
    }
}
