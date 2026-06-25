<?php

namespace App\Http\Requests\Aitg;

use App\Models\Aitg\PlanContratacion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlanContratacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('CREAR PLAN CONTRATACION') ?? false;
    }

    public function rules(): array
    {
        return $this->planRules();
    }

    protected function planRules(): array
    {
        $tipo = $this->input('tipo_registro_perfil', 'directo');

        return [
            'competencia_id' => ['required', 'integer', 'exists:competencias,id'],
            'tipo_registro_perfil' => ['required', Rule::in(array_keys(PlanContratacion::TIPOS_REGISTRO_PERFIL))],
            'modalidad' => ['required', Rule::in(array_keys(PlanContratacion::MODALIDADES))],
            'regional_id' => ['required', 'integer', 'exists:regionals,id'],
            'periodo' => ['required', 'string', 'max:20'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'estado' => ['required', Rule::in(array_keys(PlanContratacion::ESTADOS))],
            'tope_global' => ['nullable', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
            'perfiles' => ['nullable', 'array'],
            'perfiles.*.id' => ['nullable', 'integer'],
            'perfiles.*.descripcion_criterio' => ['required_with:perfiles', 'string', 'max:2000'],
            'perfiles.*.descripcion_criterio_programa' => [
                Rule::requiredIf($tipo === 'directo'),
                'nullable',
                'string',
                'max:2000',
            ],
            'perfiles.*.incluye_experiencia' => ['nullable', 'boolean'],
            'perfiles.*.experiencia_relacionada_meses' => [
                'nullable',
                'integer',
                'min:0',
                'required_if:perfiles.*.incluye_experiencia,1,true',
            ],
            'perfiles.*.experiencia_docencia_meses' => [
                'nullable',
                'integer',
                'min:0',
                'required_if:perfiles.*.incluye_experiencia,1,true',
            ],
            'checklist' => ['nullable', 'array'],
            'checklist.*.id' => ['nullable', 'integer'],
            'checklist.*.descripcion_criterio' => ['required_with:checklist', 'string', 'max:2000'],
            'puntos_adicionales' => ['nullable', 'array'],
            'puntos_adicionales.*.id' => ['nullable', 'integer'],
            'puntos_adicionales.*.descripcion' => ['required_with:puntos_adicionales', 'string', 'max:500'],
            'puntos_adicionales.*.puntaje_adicional' => ['required_with:puntos_adicionales', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'perfiles.*.descripcion_criterio.required_with' => 'La descripción del criterio es obligatoria en cada bloque de perfil.',
            'checklist.*.descripcion_criterio.required_with' => 'La descripción del criterio es obligatoria en cada bloque del checklist.',
            'perfiles.*.descripcion_criterio_programa.required' => 'La descripción del criterio (programa) es obligatoria en registro directo.',
            'perfiles.*.experiencia_relacionada_meses.required_if' => 'Indique los meses de experiencia relacionada.',
            'perfiles.*.experiencia_docencia_meses.required_if' => 'Indique los meses de experiencia en docencia.',
        ];
    }
}
