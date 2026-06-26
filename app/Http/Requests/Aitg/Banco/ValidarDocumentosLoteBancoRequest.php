<?php

namespace App\Http\Requests\Aitg\Banco;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidarDocumentosLoteBancoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('VALIDAR DOCUMENTO BANCO AITG') ?? false;
    }

    public function rules(): array
    {
        return [
            'validaciones' => ['required', 'array', 'min:1'],
            'validaciones.*.resultado' => ['required', Rule::in(['aprobado', 'rechazado'])],
            'validaciones.*.motivo_rechazo_id' => ['nullable', 'integer', 'exists:aitg_motivos_rechazo,id'],
            'validaciones.*.descripcion' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->input('validaciones', []) as $id => $data) {
                if (($data['resultado'] ?? '') === 'rechazado' && empty($data['motivo_rechazo_id'])) {
                    $validator->errors()->add(
                        "validaciones.{$id}.motivo_rechazo_id",
                        'Seleccione el motivo de rechazo para cada documento rechazado.'
                    );
                }
            }
        });
    }
}
