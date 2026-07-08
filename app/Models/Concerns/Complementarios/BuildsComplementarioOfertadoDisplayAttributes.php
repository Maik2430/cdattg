<?php

namespace App\Models\Concerns\Complementarios;

trait BuildsComplementarioOfertadoDisplayAttributes
{
    public function getIconoAttribute()
    {
        $iconos = [
            'Auxiliar de Cocina' => 'fas fa-utensils',
            'Acabados en Madera' => 'fas fa-hammer',
            'Confección de Prendas' => 'fas fa-cut',
            'Mecánica Básica Automotriz' => 'fas fa-car',
            'Cultivos de Huertas Urbanas' => 'fas fa-spa',
            'Normatividad Laboral' => 'fas fa-gavel',
        ];

        return $iconos[$this->nombre] ?? 'fas fa-graduation-cap';
    }

    /**
     * Calcular tasa de aceptación de aspirantes
     *
     * Este accessor calcula la tasa de aceptación basándose en los atributos
     * total_aspirantes y aceptados que pueden venir de consultas agregadas.
     *
     * @return float Tasa de aceptación en porcentaje (0-100)
     */
    public function getTasaAceptacionAttribute(): float
    {
        // Los atributos agregados se almacenan directamente en $this->attributes
        $totalAspirantes = $this->attributes['total_aspirantes'] ?? 0;
        $aceptados = $this->attributes['aceptados'] ?? 0;

        if ($totalAspirantes > 0 && is_numeric($aceptados) && is_numeric($totalAspirantes)) {
            return round(($aceptados / $totalAspirantes) * 100, 1);
        }

        return 0.0;
    }
}
