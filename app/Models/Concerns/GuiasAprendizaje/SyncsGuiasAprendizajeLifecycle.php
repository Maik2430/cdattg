<?php

namespace App\Models\Concerns\GuiasAprendizaje;

trait SyncsGuiasAprendizajeLifecycle
{
    protected static function bootSyncsGuiasAprendizajeLifecycle(): void
    {
        static::creating(function ($guia) {
            $guia->status = $guia->status ?? 1;
        });

        static::saving(function ($guia) {
            if ($guia->codigo) {
                $guia->codigo = strtoupper($guia->codigo);
            }
            if ($guia->nombre) {
                $guia->nombre = strtoupper($guia->nombre);
            }
        });
    }
}
