<?php

namespace App\Livewire\Concerns\PersonaImport;

use App\Models\PersonaImport;
use Illuminate\Support\Str;

trait HandlesPersonaImportHistoryActions
{
    public function cargarImportaciones(): void
    {
        $this->importaciones = PersonaImport::query()
            ->with(['user.persona'])
            ->withCount('issues')
            ->orderByDesc('created_at')
            ->limit(12)
            ->get()
            ->toArray();

        $this->importacionesActivas = PersonaImport::query()
            ->whereIn('status', ['pending', 'processing'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($import) {
                return [
                    'id' => $import->id,
                    'nombre' => Str::limit($import->original_name, 38),
                ];
            })
            ->toArray();
    }

    public function recargarHistorial(): void
    {
        $this->cargarImportaciones();
        if ($this->importacionId) {
            $this->actualizarProgreso();
        }
    }
}
