<?php

namespace App\Repositories\Concerns\Ficha;

use App\Models\FichaCaracterizacion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

trait HandlesFichaRepositoryQueryActions
{
    public function obtenerActivas(): Collection
    {
        return $this->cache('activas', function () {
            return FichaCaracterizacion::where('status', 1)
                ->with(['programaFormacion', 'jornadaFormacion.parametro', 'modalidadFormacion', 'ambiente.piso.bloque.sede'])
                ->orderBy('ficha')
                ->get();
        }, 60);
    }

    public function obtenerConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $query = FichaCaracterizacion::with([
            'programaFormacion.redConocimiento',
            'jornadaFormacion.parametro',
            'modalidadFormacion',
            'ambiente.piso.bloque.sede',
            'regional',
            'instructor.persona',
            'sede',
        ])->withCount([
            'aprendices' => function ($query): void {
                $query->whereNull('aprendices.deleted_at')
                    ->where('aprendices.estado', 1);
            },
        ]);

        if (! empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('ficha', 'LIKE', "%{$search}%")
                    ->orWhereHas('programaFormacion', function ($pq) use ($search): void {
                        $pq->where('nombre', 'LIKE', "%{$search}%");
                    });
            });
        }

        if (isset($filtros['status'])) {
            $query->where('status', $filtros['status']);
        }

        if (! empty($filtros['programa_id'])) {
            $query->where('programa_formacion_id', $filtros['programa_id']);
        }

        if (! empty($filtros['jornada_id'])) {
            $query->where('jornada_formacion_id', $filtros['jornada_id']);
        }

        if (! empty($filtros['regional_id'])) {
            $query->where('regional_id', $filtros['regional_id']);
        }

        $perPage = $filtros['per_page'] ?? 15;

        return $query->orderBy('fecha_inicio', 'desc')->paginate($perPage);
    }

    public function encontrarConRelaciones(int $id): ?FichaCaracterizacion
    {
        return $this->cache("ficha.{$id}", function () use ($id) {
            return FichaCaracterizacion::with([
                'programaFormacion.redConocimiento',
                'jornadaFormacion.parametro',
                'modalidadFormacion',
                'ambiente.piso.bloque.sede',
                'regional',
                'diasFormacion',
            ])->find($id);
        }, 60);
    }

    public function obtenerVigentes(): Collection
    {
        return $this->cache('vigentes', function () {
            return FichaCaracterizacion::where('status', 1)
                ->where('fecha_fin', '>=', now())
                ->with(['programaFormacion', 'jornadaFormacion.parametro'])
                ->orderBy('fecha_inicio')
                ->get();
        }, 30);
    }

    public function obtenerPorPrograma(int $programaId): Collection
    {
        return $this->cache("programa.{$programaId}.fichas", function () use ($programaId) {
            return FichaCaracterizacion::where('programa_formacion_id', $programaId)
                ->where('status', 1)
                ->orderBy('ficha')
                ->get();
        }, 60);
    }

    public function obtenerEstadisticas(): array
    {
        return $this->cache('estadisticas', function () {
            return [
                'total' => FichaCaracterizacion::count(),
                'activas' => FichaCaracterizacion::where('status', 1)->count(),
                'vigentes' => FichaCaracterizacion::where('status', 1)
                    ->where('fecha_fin', '>=', now())
                    ->count(),
                'finalizadas' => FichaCaracterizacion::where('fecha_fin', '<', now())->count(),
            ];
        }, 15);
    }
}
