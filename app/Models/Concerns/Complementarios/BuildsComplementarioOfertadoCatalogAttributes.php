<?php

namespace App\Models\Concerns\Complementarios;

trait BuildsComplementarioOfertadoCatalogAttributes
{
    public ?string $pendingCatalogoDenominacion = null;

    /**
     * Accessor para obtener la modalidad (ParametroTema) desde el catálogo.
     */
    public function getModalidadAttribute()
    {
        if (! $this->catalogo_id) {
            return null;
        }

        $this->loadMissing(['catalogo.modalidad.parametro']);

        return $this->catalogo?->modalidad;
    }

    /**
     * Accessor para obtener modalidad_id desde el catálogo
     * Mantiene compatibilidad hacia atrás con código que usa $complementario->modalidad_id
     */
    public function getModalidadIdAttribute(): ?int
    {
        if ($this->catalogo_id && $this->relationLoaded('catalogo')) {
            return $this->catalogo?->modalidad_id;
        }

        if ($this->catalogo_id) {
            $this->loadMissing('catalogo');

            return $this->catalogo?->modalidad_id;
        }

        return null;
    }

    /**
     * Accessor para obtener el nombre desde el catálogo
     * Mantiene compatibilidad hacia atrás con código que usa $complementario->nombre
     */
    public function getNombreAttribute(): ?string
    {
        if ($this->catalogo_id && $this->relationLoaded('catalogo')) {
            return $this->catalogo?->denominacion;
        }

        if ($this->catalogo_id) {
            $this->loadMissing('catalogo');

            return $this->catalogo?->denominacion;
        }

        return null;
    }

    /**
     * Redirige asignaciones legacy de nombre hacia el catálogo (denominacion).
     * Evita INSERT/UPDATE sobre una columna que ya no existe en complementarios_ofertados.
     */
    public function setNombreAttribute(?string $value): void
    {
        if ($value === null) {
            return;
        }

        if ($this->exists && $this->catalogo_id) {
            $this->loadMissing('catalogo');
            $this->catalogo?->update(['denominacion' => $value]);

            return;
        }

        $this->pendingCatalogoDenominacion = $value;
    }

    /**
     * Aplica denominación pendiente tras crear el ofertado (p. ej. factory con ['nombre' => ...]).
     */
    protected static function bootBuildsComplementarioOfertadoCatalogAttributes(): void
    {
        static::created(function ($ofertado): void {
            if ($ofertado->pendingCatalogoDenominacion === null || ! $ofertado->catalogo_id) {
                return;
            }

            $ofertado->loadMissing('catalogo');
            $ofertado->catalogo?->update([
                'denominacion' => $ofertado->pendingCatalogoDenominacion,
            ]);
            $ofertado->pendingCatalogoDenominacion = null;
        });
    }

    /**
     * Accessor para obtener la duración desde el catálogo
     * Mantiene compatibilidad hacia atrás con código que usa $complementario->duracion
     */
    public function getDuracionAttribute(): ?int
    {
        if ($this->catalogo_id && $this->relationLoaded('catalogo')) {
            return $this->catalogo?->duracion_horas;
        }

        if ($this->catalogo_id) {
            $this->loadMissing('catalogo');

            return $this->catalogo?->duracion_horas;
        }

        return null;
    }

    /**
     * Accessor para obtener los requisitos de ingreso desde el catálogo
     * Mantiene compatibilidad hacia atrás con código que usa $complementario->requisitos_ingreso
     */
    public function getRequisitosIngresoAttribute(): ?string
    {
        if ($this->catalogo_id && $this->relationLoaded('catalogo')) {
            return $this->catalogo?->requisitos_ingreso;
        }

        if ($this->catalogo_id) {
            $this->loadMissing('catalogo');

            return $this->catalogo?->requisitos_ingreso;
        }

        return null;
    }
}
