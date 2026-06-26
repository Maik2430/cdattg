<?php

namespace App\Http\Requests\Aitg\Convocatoria;

use App\Models\Aitg\Convocatoria\Convocatoria;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConvocatoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('CREAR CONVOCATORIA AITG') ?? false;
    }

    public function rules(): array
    {
        return $this->reglas();
    }

    protected function reglas(): array
    {
        $estado = $this->input('estado', 'borrador');
        $esPublicada = $estado === 'publicada';

        return [
            'titulo' => ['required', 'string', 'max:255'],
            'competencia_id' => ['required', 'integer', 'exists:competencias,id'],
            'plan_contratacion_id' => ['required', 'integer', 'exists:aitg_planes_contratacion,id'],
            'descripcion' => ['nullable', 'string'],
            'objeto_contractual' => ['nullable', 'string'],
            'requisitos' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(Convocatoria::ESTADOS_MANUALES)],
            'codigo_cdp' => ['nullable', 'string', 'max:100'],
            'valor_total' => ['nullable', 'numeric', 'min:0'],
            'valor_contrato_honorarios' => ['nullable', 'numeric', 'min:0'],
            'fecha_inicio_publicacion' => [$esPublicada ? 'required' : 'nullable', 'date'],
            'fecha_fin_publicacion' => [$esPublicada ? 'required' : 'nullable', 'date', 'after_or_equal:fecha_inicio_publicacion'],
            'fecha_inicio_contrato' => ['nullable', 'date'],
            'fecha_fin_contrato' => ['nullable', 'date', 'after_or_equal:fecha_inicio_contrato'],
            'centro_formacion_id' => [$esPublicada ? 'required' : 'nullable', 'integer', 'exists:centro_formacions,id'],
            'regional_id' => [$esPublicada ? 'required' : 'nullable', 'integer', 'exists:regionals,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_inicio_publicacion.required' => 'La fecha de inicio de publicación es obligatoria al publicar la convocatoria.',
            'fecha_fin_publicacion.required' => 'La fecha de fin de publicación es obligatoria al publicar la convocatoria.',
            'centro_formacion_id.required' => 'Debe indicar el centro de formación al publicar la convocatoria.',
            'regional_id.required' => 'Debe indicar la regional al publicar la convocatoria.',
        ];
    }
}
