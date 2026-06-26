<?php

namespace App\Http\Requests\Aitg\Banco;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkDocumentosBancoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('SUBIR DOCUMENTO BANCO AITG') ?? false;
    }

    public function rules(): array
    {
        return [
            'archivos' => ['required', 'array', 'min:1'],
            'archivos.*' => ['file', 'mimes:pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'archivos.required' => 'Seleccione al menos un archivo PDF para cargar.',
            'archivos.*.mimes' => 'Solo se permiten archivos PDF.',
        ];
    }
}
