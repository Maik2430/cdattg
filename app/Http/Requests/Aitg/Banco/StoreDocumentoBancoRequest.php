<?php

namespace App\Http\Requests\Aitg\Banco;

use App\Models\Aitg\Banco\TipoArchivo;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoBancoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('SUBIR DOCUMENTO BANCO AITG') ?? false;
    }

    public function rules(): array
    {
        $tipoId = $this->input('tipo_archivo_id');
        $tipo = $tipoId ? TipoArchivo::find($tipoId) : null;

        $mimes = $tipo?->reglasMimes() ?? 'pdf';
        $maxKb = $tipo?->tamano_max_kb ?? 10240;

        return [
            'tipo_archivo_id' => ['nullable', 'integer', 'exists:aitg_tipos_archivo,id'],
            'punto_adicional_id' => ['nullable', 'integer', 'exists:aitg_puntos_adicionales,id'],
            'checklist_item_id' => ['nullable', 'integer'],
            'punto_item_id' => ['nullable', 'integer'],
            'perfil_plan_id' => ['nullable', 'integer', 'exists:aitg_perfiles_plan,id'],
            'archivo' => ['required', 'file', "mimes:{$mimes}", "max:{$maxKb}"],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $tieneDestino = $this->filled('tipo_archivo_id')
                || $this->filled('punto_adicional_id')
                || $this->filled('checklist_item_id')
                || $this->filled('punto_item_id')
                || $this->filled('perfil_plan_id');

            if (! $tieneDestino) {
                $validator->errors()->add('archivo', 'No se identificó el documento destino. Recargue la página e intente de nuevo.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'archivo.required' => 'Seleccione un archivo PDF.',
            'archivo.mimes' => 'Solo se permiten archivos PDF.',
            'archivo.max' => 'El PDF supera el tamaño máximo permitido (10 MB).',
        ];
    }
}
