<?php

namespace App\Models\Concerns\Complementarios;

trait BuildsComplementarioOfertadoCatalogAttributes
{
    /**
     * Accessor para obtener la modalidad desde el catálogo
     * Mantiene compatibilidad hacia atrás con código que usa $complementario->modalidad
     */
    public function getModalidadAttribute()
    {
        if ($this->catalogo_id && $this->relationLoaded('catalogo')) {
            return $this->catalogo?->modalidad;
        }

        if ($this->catalogo_id) {
            $this->loadMissing(['catalogo.modalidad.parametro']);

            return $this->catalogo?->modalidad;
        }

        return null;
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
