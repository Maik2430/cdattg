<?php

namespace App\Http\Requests\Aitg\Banco;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMotivoRechazoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('CREAR MOTIVO RECHAZO AITG') ?? false;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:50', 'unique:aitg_motivos_rechazo,codigo'],
            'nombre' => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
