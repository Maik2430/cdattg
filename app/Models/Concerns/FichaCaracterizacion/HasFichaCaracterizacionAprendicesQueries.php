<?php

namespace App\Models\Concerns\FichaCaracterizacion;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasFichaCaracterizacionAprendicesQueries
{
    /**
     * Obtiene solo los aprendices activos de esta ficha.
     * Nota: La relación aprendices() ya filtra por estado activo, este método es redundante pero se mantiene por compatibilidad.
     */
    public function aprendicesActivos(): HasMany
    {
        return $this->aprendices();
    }

    /**
     * Obtiene el conteo total de aprendices en esta ficha (activos e inactivos).
     * Se usa para validaciones.
     */
    public function contarAprendices(): int
    {
        return $this->aprendicesTodos()->count();
    }

    /**
     * Obtiene el conteo de aprendices activos en esta ficha.
     */
    public function contarAprendicesActivos(): int
    {
        return $this->aprendicesActivos()->count();
    }

    /**
     * Verifica si la ficha tiene aprendices asignados (activos e inactivos).
     * Se usa para validaciones.
     */
    public function tieneAprendices(): bool
    {
        return $this->aprendicesTodos()->exists();
    }

    /**
     * Verifica si un aprendiz específico pertenece a esta ficha (activo o inactivo).
     */
    public function tieneAprendiz(int $aprendizId): bool
    {
        return $this->aprendicesTodos()->where('aprendices.id', $aprendizId)->exists();
    }
}
