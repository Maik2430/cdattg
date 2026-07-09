<?php

namespace App\Services\Concerns\Ambiente;

use App\Models\Ambiente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesAmbienteWriteActions
{
    public function crear(array $datos): Ambiente
    {
        return DB::transaction(function () use ($datos) {
            $ambiente = Ambiente::create($datos);

            $this->repository->invalidarCache();

            Log::info('Ambiente creado', [
                'ambiente_id' => $ambiente->id,
                'nombre' => $ambiente->title,
            ]);

            return $ambiente;
        });
    }

    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $actualizado = Ambiente::where('id', $id)->update($datos);

            if ($actualizado) {
                $this->repository->invalidarCache();

                Log::info('Ambiente actualizado', [
                    'ambiente_id' => $id,
                ]);
            }

            return $actualizado;
        });
    }

    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $ambiente = Ambiente::find($id);

            if (! $ambiente) {
                throw new \Exception('Ambiente no encontrado');
            }

            $eliminado = $ambiente->delete();

            if ($eliminado) {
                $this->repository->invalidarCache();

                Log::info('Ambiente eliminado', [
                    'ambiente_id' => $id,
                ]);
            }

            return $eliminado;
        });
    }

    public function cambiarEstado(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $ambiente = Ambiente::find($id);

            if (! $ambiente) {
                throw new \Exception('Ambiente no encontrado');
            }

            $nuevoEstado = ! $ambiente->status;
            $ambiente->update(['status' => $nuevoEstado]);

            $this->repository->invalidarCache();

            Log::info('Estado de ambiente cambiado', [
                'ambiente_id' => $id,
                'nuevo_estado' => $nuevoEstado,
            ]);

            return true;
        });
    }
}
