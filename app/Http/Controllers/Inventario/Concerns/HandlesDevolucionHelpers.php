<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario\Concerns;

use Illuminate\Support\Facades\Auth;

trait HandlesDevolucionHelpers
{
    private function getEstadoOrdenAprobadaId(): int
    {
        $estadoAprobada = $this->service->obtenerEstadoAprobada();

        return (int) $estadoAprobada->id;
    }

    protected function resolveDevolucionUserIdFilter(): ?int
    {
        $user = Auth::user();

        if ($user !== null && ! $user->can('VER TODAS LAS ORDENES')) {
            return (int) $user->id;
        }

        return null;
    }
}
