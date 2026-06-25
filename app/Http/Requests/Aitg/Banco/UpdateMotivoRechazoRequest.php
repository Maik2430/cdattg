<?php

namespace App\Http\Requests\Aitg\Banco;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMotivoRechazoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('EDITAR MOTIVO RECHAZO AITG') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('motivoRechazo')?->id ?? $this->route('motivoRechazo');

        return [
            'codigo' => ['required', 'string', 'max:50', Rule::unique('aitg_motivos_rechazo', 'codigo')->ignore($id)],
            'nombre' => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
