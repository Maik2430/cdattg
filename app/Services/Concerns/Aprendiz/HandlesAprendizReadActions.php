<?php

namespace App\Services\Concerns\Aprendiz;

use App\Models\Aprendiz;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

trait HandlesAprendizReadActions
{
    /**
     * Obtiene lista paginada de aprendices con filtros
     */
    public function listarConFiltros(array $filtros = []): LengthAwarePaginator
    {
        return $this->repository->obtenerAprendicesConFiltros($filtros);
    }

    /**
     * Obtiene un aprendiz por ID con todas sus relaciones
     */
    public function obtener(int $id): ?Aprendiz
    {
        return $this->repository->encontrarConRelaciones($id);
    }

    /**
     * Busca aprendices por término
     */
    public function buscar(string $termino, int $limite = 10): Collection
    {
        return $this->repository->buscar($termino, $limite);
    }

    /**
     * Obtiene aprendices por ficha
     */
    public function obtenerPorFicha(int $fichaId): Collection
    {
        return $this->repository->obtenerPorFicha($fichaId);
    }

    /**
     * Obtiene estadísticas de aprendices
     */
    public function obtenerEstadisticas(): array
    {
        return $this->repository->obtenerEstadisticas();
    }

    /**
     * Verifica si una persona ya es aprendiz
     */
    public function esAprendiz(int $personaId): bool
    {
        return $this->repository->esAprendiz($personaId);
    }

    /**
     * Cuenta aprendices por ficha
     */
    public function contarPorFicha(int $fichaId): int
    {
        return $this->repository->contarPorFicha($fichaId);
    }
}
