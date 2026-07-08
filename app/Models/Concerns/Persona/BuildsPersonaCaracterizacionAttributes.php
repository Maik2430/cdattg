<?php

namespace App\Models\Concerns\Persona;

trait BuildsPersonaCaracterizacionAttributes
{
    /**
     * @return array<int, string>
     */
    public function getCaracterizacionesComplementariasNombresAttribute(): array
    {
        return $this->caracterizacionesComplementarias
            ->pluck('nombre')
            ->filter()
            ->values()
            ->all();
    }

    public function getCaracterizacionesComplementariasTextoAttribute(): string
    {
        $nombres = $this->caracterizaciones_complementarias_nombres;

        return $nombres ? implode(', ', $nombres) : '';
    }
}
