<?php

namespace App\Services\Concerns\Instructor;

use Illuminate\Database\Eloquent\Builder;

trait HandlesInstructorListadoQueryHelpers
{
    private function normalizarFiltroEstado(mixed $estado): ?bool
    {
        if ($estado === null || $estado === '' || $estado === 'todos') {
            return null;
        }

        if (is_bool($estado)) {
            return $estado;
        }

        if (is_int($estado)) {
            return $estado === 1;
        }

        $estadoNormalizado = strtolower(trim((string) $estado));

        if (in_array($estadoNormalizado, ['1', 'true', 'activo', 'activos'], true)) {
            return true;
        }

        if (in_array($estadoNormalizado, ['0', 'false', 'inactivo', 'inactivos'], true)) {
            return false;
        }

        return null;
    }

    private function aplicarOrdenamiento(Builder $query, ?string $sortField, string $sortDirection): void
    {
        $direction = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        if ($sortField === 'nombre') {
            $query
                ->join('personas', 'instructors.persona_id', '=', 'personas.id')
                ->orderBy('personas.primer_nombre', $direction)
                ->orderBy('personas.primer_apellido', $direction)
                ->select('instructors.*');

            return;
        }

        if ($sortField === 'created_at') {
            $query->orderBy('instructors.created_at', $direction);

            return;
        }

        $query->orderBy('instructors.id', 'desc');
    }
}
