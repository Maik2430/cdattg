<?php

namespace App\Models\Concerns\Complementarios;

use App\Models\ParametroTema;

trait BuildsComplementarioOfertadoEstadoAttributes
{
    /**
     * Accessor para compatibilidad hacia atrás con código que espera el campo 'estado'
     * Devuelve el valor numérico legacy basado en el nombre del parámetro
     * Nota: Para evitar referencia circular, no usamos $this->estado
     */
    public function getEstadoAttribute()
    {
        // Obtener el estado_id directamente del atributo
        $estadoId = $this->attributes['estado_id'] ?? null;

        if (! $estadoId) {
            return 0; // Valor por defecto: Sin Oferta
        }

        // Intentar obtener el nombre del parámetro a través de la relación
        try {
            // Usar una consulta directa para evitar referencia circular
            $parametroTema = ParametroTema::with('parametro')->find($estadoId);
            if ($parametroTema && $parametroTema->parametro) {
                $nombre = strtoupper(trim($parametroTema->parametro->name));

                return match ($nombre) {
                    'SIN OFERTA' => 0,
                    'CON OFERTA' => 1,
                    'CUPOS LLENOS' => 2,
                    default => 0,
                };
            }
        } catch (\Exception $e) {
            // Si hay error, retornar valor por defecto
        }

        return 0;
    }

    public function getEstadoLabelAttribute()
    {
        // Obtener el estado_id directamente del atributo
        $estadoId = $this->attributes['estado_id'] ?? null;

        if (! $estadoId) {
            return 'Desconocido';
        }

        $label = 'Desconocido';

        // Intentar obtener el nombre del parámetro a través de la relación
        try {
            /** @var ParametroTema|null $estadoRelacion */
            $estadoRelacion = null;

            if ($this->relationLoaded('estado')) {
                $estadoRelacion = $this->getRelation('estado');
            }

            if ($estadoRelacion instanceof ParametroTema) {
                if (! $estadoRelacion->relationLoaded('parametro')) {
                    $estadoRelacion->load('parametro');
                }
                if ($this->estado->parametro) {
                    $label = $this->estado->parametro->name;
                }
            } else {
                // Si la relación no está cargada, hacer una consulta
                $estado = $this->estado()->with('parametro')->first();
                if ($estado && $estado->parametro) {
                    $label = $estado->parametro->name;
                }
            }
        } catch (\Exception $e) {
            // Si hay error, mantener el valor por defecto
        }

        return $label;
    }

    public function getBadgeClassAttribute()
    {
        $estadoNombre = $this->estado_label;

        return match ($estadoNombre) {
            'SIN OFERTA' => 'bg-success',
            'CON OFERTA' => 'bg-warning',
            'CUPOS LLENOS' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
