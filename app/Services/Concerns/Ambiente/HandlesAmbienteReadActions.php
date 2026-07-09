<?php

namespace App\Services\Concerns\Ambiente;

use App\Models\Ambiente;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait HandlesAmbienteReadActions
{
    public function listar(int $perPage = 10): LengthAwarePaginator
    {
        return Ambiente::with(['piso.bloque', 'sede'])->paginate($perPage);
    }

    public function obtener(int $id): ?Ambiente
    {
        return Ambiente::with(['piso.bloque', 'sede'])->find($id);
    }

    public function obtenerPorPiso(int $pisoId): array
    {
        $ambientes = Ambiente::where('piso_id', $pisoId)->get();

        return [
            'success' => true,
            'ambientes' => $ambientes,
        ];
    }

    public function obtenerPorRegional(int $regionalId): array
    {
        $regional = \App\Models\Regional::find($regionalId);

        if (! $regional) {
            return [
                'success' => false,
                'message' => 'Regional no encontrada',
            ];
        }

        $ambientes = [];

        foreach ($regional->sedes as $sede) {
            foreach ($sede->bloques as $bloque) {
                foreach ($bloque->piso as $piso) {
                    $ambientes = array_merge($ambientes, $piso->ambientes->toArray());
                }
            }
        }

        return [
            'success' => true,
            'ambientes' => $ambientes,
        ];
    }
}
