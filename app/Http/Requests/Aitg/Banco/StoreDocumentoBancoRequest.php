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
        /** @var TipoArchivo|null $tipo */
        $tipo = TipoArchivo::find($this->input('tipo_archivo_id'));

        $mimes = $tipo?->reglasMimes() ?? 'pdf';
        $maxKb = $tipo?->tamano_max_kb ?? 5120;

        return [
            'tipo_archivo_id' => ['required', 'integer', 'exists:aitg_tipos_archivo,id'],
            'archivo' => ['required', 'file', "mimes:{$mimes}", "max:{$maxKb}"],
        ];
    }
}
