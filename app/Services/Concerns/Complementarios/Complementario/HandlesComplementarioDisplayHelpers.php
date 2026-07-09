<?php

namespace App\Services\Concerns\Complementarios\Complementario;

use App\Models\Complementarios\ComplementarioOfertado;
use Illuminate\Database\Eloquent\Collection;

trait HandlesComplementarioDisplayHelpers
{
    /**
     * Obtener icono para un programa complementario
     */
    public function getIconoForPrograma(string $nombre): string
    {
        $iconos = [
            'Auxiliar de Cocina' => 'fas fa-utensils',
            'Acabados en Madera' => 'fas fa-hammer',
            'Confección de Prendas' => 'fas fa-cut',
            'Mecánica Básica Automotriz' => 'fas fa-car',
            'Cultivos de Huertas Urbanas' => 'fas fa-spa',
            'Normatividad Laboral' => 'fas fa-gavel',
        ];

        return $iconos[$nombre] ?? 'fas fa-graduation-cap';
    }

    /**
     * Obtener clase CSS para el badge según el estado del programa
     */
    public function getBadgeClassForEstado(int $estado): string
    {
        $badgeClasses = [
            0 => 'bg-secondary',
            1 => 'bg-success',
            2 => 'bg-warning',
        ];

        return $badgeClasses[$estado] ?? 'bg-secondary';
    }

    /**
     * Obtener label del estado del programa
     */
    public function getEstadoLabel(int $estado): string
    {
        $estados = [
            0 => 'Sin Oferta',
            1 => 'Con Oferta',
            2 => 'Cupos Llenos',
        ];

        return $estados[$estado] ?? 'Desconocido';
    }

    /**
     * Convertir valor legacy de estado (0,1,2) a estado_id (ID de ParametroTema)
     */
    public function convertirEstadoLegacyAEstadoId(int $estadoLegacy): ?int
    {
        return $this->programaRepository->getEstadoIdByLegacyValue($estadoLegacy);
    }

    /**
     * Enriquecer un programa con datos auxiliares para la vista.
     */
    public function enriquecerPrograma(ComplementarioOfertado $programa): ComplementarioOfertado
    {
        $programa->icono = $this->getIconoForPrograma($programa->nombre);
        $programa->modalidad_nombre = $programa->catalogo?->modalidad?->parametro?->name ?? null;
        $programa->jornada_nombre = $programa->jornada->jornada ?? null;

        return $programa;
    }

    /**
     * Enriquecer una colección de programas complementarios.
     */
    public function enriquecerProgramas(Collection $programas): Collection
    {
        return $programas->map(function (ComplementarioOfertado $programa): ComplementarioOfertado {
            return $this->enriquecerPrograma($programa);
        });
    }
}
