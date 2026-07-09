<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inventario\Concerns;

use App\Exceptions\DevolucionException;
use App\Http\Requests\Inventario\DevolucionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

trait HandlesDevolucionWriteActions
{
    // Registrar devolución
    public function store(DevolucionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ((int) $validated['cantidad_devuelta'] === 0) {
            $observaciones = $validated['observaciones'] ?? '';
            if (trim($observaciones) === '') {
                throw ValidationException::withMessages([
                    'observaciones' => 'Debes indicar el motivo cuando registras una devolución de cantidad cero.',
                ]);
            }
        }

        try {
            $resultado = $this->service->registrarDevolucionConMensaje(
                (int) $validated['detalle_orden_id'],
                (int) $validated['cantidad_devuelta'],
                $validated['observaciones'] ?? null
            );

            return redirect()
                ->route('inventario.devoluciones.index')
                ->with('success', $resultado['mensaje']);

        } catch (DevolucionException $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al registrar la devolución: '.$e->getMessage());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error inesperado al registrar la devolución: '.$e->getMessage());
        }
    }
}
