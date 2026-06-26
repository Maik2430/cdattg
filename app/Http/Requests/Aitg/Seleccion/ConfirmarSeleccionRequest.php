<?php

namespace App\Http\Requests\Aitg\Seleccion;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmarSeleccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('SELECCIONAR INSTRUCTOR AITG') ?? false;
    }

    public function rules(): array
    {
        return [
            'postulacion_ganador_id' => ['required', 'integer', 'exists:aitg_postulaciones_plan,id'],
            'postulacion_suplente_id' => ['nullable', 'integer', 'exists:aitg_postulaciones_plan,id', 'different:postulacion_ganador_id'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
