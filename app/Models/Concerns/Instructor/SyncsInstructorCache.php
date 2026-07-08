<?php

namespace App\Models\Concerns\Instructor;

trait SyncsInstructorCache
{
    protected static function bootSyncsInstructorCache(): void
    {
        static::creating(function ($instructor) {
            $instructor->status = $instructor->status ?? true;
            $instructor->actualizarCache();
        });

        static::updating(function ($instructor) {
            $instructor->actualizarCache();
        });
    }

    /**
     * Actualizar campos de caché para optimizar búsquedas.
     */
    public function actualizarCache(): void
    {
        if ($this->persona) {
            $this->numero_documento_cache = $this->persona->numero_documento;
            $this->nombre_completo_cache = $this->getNombreCompletoAttribute();
        }
    }
}
