<?php

namespace App\Http\Requests\Aitg\Banco;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTipoArchivoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('EDITAR TIPO ARCHIVO AITG') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('tipoArchivo')?->id ?? $this->route('tipoArchivo');

        return [
            'codigo' => ['required', 'string', 'max:50', Rule::unique('aitg_tipos_archivo', 'codigo')->ignore($id)],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'extensiones_permitidas' => ['nullable'],
            'tamano_max_kb' => ['required', 'integer', 'min:100', 'max:20480'],
            'es_obligatorio' => ['nullable', 'boolean'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
