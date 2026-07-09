<?php

namespace App\Repositories\Concerns\Aprendiz;

use App\Models\Aprendiz;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

trait HandlesAprendizRepositoryQueryActions
{
    public function obtenerAprendicesConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $query = Aprendiz::with(['persona.tipoDocumento', 'fichaCaracterizacion.programaFormacion'])
            ->whereIn('id', function ($subquery): void {
                $subquery->select(DB::raw('MAX(id)'))
                    ->from('aprendices')
                    ->whereNull('deleted_at')
                    ->groupBy('persona_id');
            });

        if (! empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->whereHas('persona', function ($q) use ($search): void {
                $q->where('primer_nombre', 'LIKE', "%{$search}%")
                    ->orWhere('segundo_nombre', 'LIKE', "%{$search}%")
                    ->orWhere('primer_apellido', 'LIKE', "%{$search}%")
                    ->orWhere('segundo_apellido', 'LIKE', "%{$search}%")
                    ->orWhere('numero_documento', 'LIKE', "%{$search}%");
            });
        }

        if (! empty($filtros['ficha_id'])) {
            $query->where('ficha_caracterizacion_id', $filtros['ficha_id']);
        }

        if (isset($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        $perPage = $filtros['per_page'] ?? 15;

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function encontrarConRelaciones(int $id): ?Aprendiz
    {
        return Aprendiz::with([
            'persona.tipoDocumento',
            'fichaCaracterizacion.programaFormacion',
            'fichaCaracterizacion.jornadaFormacion.parametro',
            'fichaCaracterizacion.modalidadFormacion',
            'asistencias' => function ($query): void {
                $query->latest()->limit(20);
            },
        ])->find($id);
    }

    public function obtenerPorFicha(int $fichaId): Collection
    {
        return Aprendiz::with(['persona.tipoDocumento'])
            ->where('ficha_caracterizacion_id', $fichaId)
            ->where('estado', true)
            ->orderBy('persona_id')
            ->get();
    }

    public function buscar(string $termino, int $limite = 10): Collection
    {
        return Aprendiz::with('persona')
            ->whereHas('persona', function ($query) use ($termino): void {
                $query->where('primer_nombre', 'LIKE', "%{$termino}%")
                    ->orWhere('segundo_nombre', 'LIKE', "%{$termino}%")
                    ->orWhere('primer_apellido', 'LIKE', "%{$termino}%")
                    ->orWhere('segundo_apellido', 'LIKE', "%{$termino}%")
                    ->orWhere('numero_documento', 'LIKE', "%{$termino}%");
            })
            ->limit($limite)
            ->get();
    }

    public function contarPorFicha(int $fichaId): int
    {
        return Aprendiz::where('ficha_caracterizacion_id', $fichaId)
            ->where('estado', true)
            ->count();
    }

    public function esAprendiz(int $personaId): bool
    {
        return Aprendiz::where('persona_id', $personaId)->exists();
    }

    public function obtenerEstadisticas(): array
    {
        return $this->cache('estadisticas', function () {
            return [
                'total' => Aprendiz::count(),
                'activos' => Aprendiz::where('estado', true)->count(),
                'inactivos' => Aprendiz::where('estado', false)->count(),
                'por_ficha' => Aprendiz::select('ficha_caracterizacion_id', DB::raw('count(*) as total'))
                    ->where('estado', true)
                    ->groupBy('ficha_caracterizacion_id')
                    ->get()
                    ->pluck('total', 'ficha_caracterizacion_id')
                    ->toArray(),
            ];
        }, 15);
    }
}
