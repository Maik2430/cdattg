<?php

namespace App\Http\Requests\Concerns\UpdateFichaCaracterizacion;

trait HandlesUpdateFichaCaracterizacionPrepareForValidation
{
    protected function prepareForValidation(): void
    {
        if (! $this->has('status')) {
            $this->merge(['status' => true]);
        }

        if (! $this->has('total_horas')) {
            $this->merge(['total_horas' => 0]);
        }
    }
}
