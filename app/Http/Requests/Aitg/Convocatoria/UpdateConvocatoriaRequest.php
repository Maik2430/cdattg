<?php

namespace App\Http\Requests\Aitg\Convocatoria;

class UpdateConvocatoriaRequest extends StoreConvocatoriaRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('EDITAR CONVOCATORIA AITG') ?? false;
    }
}
