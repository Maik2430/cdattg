<?php

namespace App\Services\Concerns\Instructor;

use App\Models\Instructor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

trait HandlesInstructorListadoActions
{
    /**
     * Lista instructores con filtros y paginación
     *
     * @param  array<string, mixed>  $filtros
     */
    public function listarConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $query = Instructor::with([
            'persona.tipoDocumento',
            'regional',
            'instructorFichas' => function ($q) {
                $q->with('ficha.programaFormacion');
            },
        ])
            ->whereHas('user.roles', function (Builder $roleQuery) {
                $roleQuery->where('name', 'INSTRUCTOR');
            });

        if (! empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('persona', function ($personaQuery) use ($search) {
                    $personaQuery->where('primer_nombre', 'like', "%{$search}%")
                        ->orWhere('segundo_nombre', 'like', "%{$search}%")
                        ->orWhere('primer_apellido', 'like', "%{$search}%")
                        ->orWhere('segundo_apellido', 'like', "%{$search}%")
                        ->orWhere('numero_documento', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $estado = $this->normalizarFiltroEstado($filtros['estado'] ?? 'todos');
        if ($estado !== null) {
            $query->where('status', $estado);
        }

        if (! empty($filtros['especialidad'])) {
            $especialidadId = (int) $filtros['especialidad'];

            $query->where(function (Builder $especialidadQuery) use ($especialidadId) {
                $especialidadQuery
                    ->whereJsonContains('especialidades->principal', $especialidadId)
                    ->orWhereJsonContains('especialidades->secundarias', $especialidadId)
                    ->orWhereJsonContains('especialidades', $especialidadId);
            });
        }

        if (! empty($filtros['regional'])) {
            $query->where('regional_id', $filtros['regional']);
        }

        $perPage = max((int) ($filtros['per_page'] ?? 15), 1);

        $this->aplicarOrdenamiento(
            $query,
            $filtros['sort_field'] ?? null,
            $filtros['sort_direction'] ?? 'desc'
        );

        return $query->paginate($perPage)->withQueryString();
    }
}
