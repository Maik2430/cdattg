<?php

namespace App\Http\Requests\Aitg\Banco;

use App\Models\Aitg\Banco\TipoArchivo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTipoArchivoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('CREAR TIPO ARCHIVO AITG') ?? false;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:50', 'unique:aitg_tipos_archivo,codigo'],
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'categoria' => ['required', Rule::in(array_keys(TipoArchivo::CATEGORIAS))],
            'fase_carga' => ['required', Rule::in(array_keys(TipoArchivo::FASES_CARGA))],
            'regla_visibilidad' => ['nullable', Rule::in(array_keys(TipoArchivo::REGLAS_VISIBILIDAD))],
            'extensiones_permitidas' => ['nullable'],
            'tamano_max_kb' => ['required', 'integer', 'min:100', 'max:20480'],
            'es_obligatorio' => ['nullable', 'boolean'],
            'permite_multiples' => ['nullable', 'boolean'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
