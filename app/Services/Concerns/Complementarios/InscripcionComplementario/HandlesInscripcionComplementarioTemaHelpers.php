<?php

namespace App\Services\Concerns\Complementarios\InscripcionComplementario;

use App\Models\Tema;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HandlesInscripcionComplementarioTemaHelpers
{
    private function obtenerCaracterizacionesAgrupadas(?object $caracterizacionesPayload = null): Collection
    {
        $payload = $caracterizacionesPayload ?? $this->buildTemaPayload(
            $this->temaRepository->obtenerCaracterizacionesComplementarias()
        );

        $parametros = collect($payload->parametros ?? []);

        if ($parametros->isEmpty()) {
            return collect();
        }

        $hijos = $parametros->map(function ($parametro) {
            $id = data_get($parametro, 'id');
            $nombre = data_get($parametro, 'name', data_get($parametro, 'nombre', ''));

            if (! $id) {
                return null;
            }

            $formatted = (string) Str::of($nombre ?? '')
                ->replace('_', ' ')
                ->lower()
                ->title();

            return (object) [
                'id' => $id,
                'nombre' => $formatted,
            ];
        })->filter()->values();

        if ($hijos->isEmpty()) {
            return collect();
        }

        return collect([
            [
                'id' => $payload->id ?? null,
                'nombre' => 'Opciones disponibles',
                'hijos' => $hijos,
            ],
        ]);
    }

    private function buildTemaPayload(?Tema $tema = null, $fallback = null): object
    {
        if ($tema !== null && $tema->parametros()->exists()) {
            return $tema;
        }

        $parametros = $this->normalizeParametrosCollection($fallback);

        return (object) [
            'parametros' => $parametros,
        ];
    }

    private function normalizeParametrosCollection($items): Collection
    {
        return collect($items)->map(function ($item) {
            $id = data_get($item, 'id');
            $name = data_get($item, 'name', data_get($item, 'nombre', ''));

            if ($id === null) {
                return null;
            }

            return (object) [
                'id' => $id,
                'name' => strtoupper((string) $name),
            ];
        })->filter()->values();
    }
}
