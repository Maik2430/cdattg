<?php

namespace App\Http\Requests\Aitg\Banco;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidarDocumentoBancoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('VALIDAR DOCUMENTO BANCO AITG') ?? false;
    }

    public function rules(): array
    {
        return [
            'resultado' => ['required', Rule::in(['aprobado', 'rechazado'])],
            'motivo_rechazo_id' => [
                Rule::requiredIf($this->input('resultado') === 'rechazado'),
                'nullable',
                'integer',
                'exists:aitg_motivos_rechazo,id',
            ],
            'descripcion' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'motivo_rechazo_id.required' => 'Seleccione el motivo por el cual no se validó el documento.',
        ];
    }
}
