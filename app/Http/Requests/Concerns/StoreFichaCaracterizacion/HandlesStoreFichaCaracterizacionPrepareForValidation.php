<?php

namespace App\Http\Requests\Concerns\StoreFichaCaracterizacion;

trait HandlesStoreFichaCaracterizacionPrepareForValidation
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->status ?? true,
            'total_horas' => $this->total_horas ?? 0,
        ]);
    }
}
