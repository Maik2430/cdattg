<?php

namespace App\Repositories\Concerns\Complementarios\ComplementarioOfertado;

use App\Models\Complementarios\ComplementarioOfertado;
use Illuminate\Database\Eloquent\Collection;

trait HandlesComplementarioOfertadoQueryActions
{
    public function getAll(array $relations = []): Collection
    {
        return ComplementarioOfertado::with($relations)->get();
    }

    public function getByEstado(int $estado, array $relations = []): Collection
    {
        $estadoId = $this->getEstadoIdByLegacyValue($estado);

        if (! $estadoId) {
            return new Collection;
        }

        return ComplementarioOfertado::with($relations)
            ->where('estado_id', $estadoId)
            ->get();
    }

    public function getActivos(array $relations = []): Collection
    {
        return $this->getByEstado(1, $relations);
    }

    public function findWithRelations(int $id, array $relations = []): ?ComplementarioOfertado
    {
        return ComplementarioOfertado::with($relations)->find($id);
    }

    public function findByNombre(string $nombre): ?ComplementarioOfertado
    {
        $nombreNormalizado = str_replace('-', ' ', $nombre);

        return ComplementarioOfertado::whereHas('catalogo', function ($query) use ($nombreNormalizado): void {
            $query->where('denominacion', $nombreNormalizado);
        })->first();
    }

    public function getAllWithAspirantesCount(array $relations = []): Collection
    {
        return ComplementarioOfertado::with($relations)
            ->withCount('aspirantes')
            ->get();
    }

    public function create(array $data): ComplementarioOfertado
    {
        return ComplementarioOfertado::create($data);
    }

    public function update(ComplementarioOfertado $programa, array $data): bool
    {
        return $programa->update($data);
    }

    public function delete(ComplementarioOfertado $programa): bool
    {
        return $programa->delete();
    }

    public function countActivos(): int
    {
        $estadoId = $this->getEstadoIdByLegacyValue(1);

        if (! $estadoId) {
            return 0;
        }

        return ComplementarioOfertado::where('estado_id', $estadoId)->count();
    }
}
