<?php

namespace App\Http\Requests\Concerns\UpdateFichaCaracterizacion;

trait HandlesUpdateFichaCaracterizacionAuthorization
{
    public function authorize(): bool
    {
        return $this->user()->can('EDITAR FICHA CARACTERIZACION');
    }
}
