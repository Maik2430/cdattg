<?php

namespace App\Livewire\Instructores\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

trait HandlesInstructorIndexRenderQueryHelpers
{
    private function buildInstructoresFiltros(): array
    {
        return [
            'search' => $this->search,
            'estado' => $this->statusFilter ?: 'todos',
            'especialidad' => $this->especialidadFilter,
            'regional' => $this->regionalFilter,
            'per_page' => $this->perPage,
            'sort_field' => $this->sortField,
            'sort_direction' => $this->sortDirection,
        ];
    }

    private function fetchPersonasConRolInstructor(): Collection
    {
        return DB::table('model_has_roles')
            ->join('personas', 'model_has_roles.model_id', '=', 'personas.id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'INSTRUCTOR')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('instructors')
                    ->whereRaw('instructors.persona_id = personas.id');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('personas.numero_documento', 'like', '%'.$this->search.'%')
                        ->orWhere('personas.primer_nombre', 'like', '%'.$this->search.'%')
                        ->orWhere('personas.primer_apellido', 'like', '%'.$this->search.'%');
                });
            })
            ->select('personas.*')
            ->get()
            ->map(function ($persona) {
                $persona->origen = 'rol';

                return $persona;
            });
    }

    private function paginateInstructoresCollection(Collection $todos): LengthAwarePaginator
    {
        $todos = $todos->sortByDesc(function ($item) {
            return $item->created_at ?? $item->id;
        })->values();

        $page = $this->page ?? 1;
        $perPage = $this->perPage;
        $total = $todos->count();
        $items = $todos->slice(($page - 1) * $perPage, $perPage);

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => Request::url(), 'query' => Request::query()]
        );
    }

    private function buildInstructoresList(): LengthAwarePaginator
    {
        $instructoresRegistrados = $this->instructorService
            ->listarConFiltros($this->buildInstructoresFiltros())
            ->map(function ($instructor) {
                $instructor->origen = 'instructor';

                return $instructor;
            });

        $personasConRol = $this->fetchPersonasConRolInstructor();

        return $this->paginateInstructoresCollection(
            $instructoresRegistrados->concat($personasConRol)
        );
    }
}
