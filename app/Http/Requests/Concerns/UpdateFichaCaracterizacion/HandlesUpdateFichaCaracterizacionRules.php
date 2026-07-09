<?php

namespace App\Http\Requests\Concerns\UpdateFichaCaracterizacion;

use Illuminate\Validation\Rule;

trait HandlesUpdateFichaCaracterizacionRules
{
    public function rules(): array
    {
        $fichaId = $this->route('fichaCaracterizacion') ?? $this->route('id');

        return [
            'ficha' => [
                'required',
                'string',
                'max:50',
                Rule::unique('fichas_caracterizacion', 'ficha')->ignore($fichaId),
            ],
            'programa_formacion_id' => [
                'required',
                'integer',
                'exists:programas_formacion,id',
            ],
            'fecha_inicio' => [
                'required',
                'date',
            ],
            'fecha_fin' => [
                'required',
                'date',
                'after_or_equal:fecha_inicio',
            ],
            'instructor_id' => [
                'nullable',
                'integer',
                'exists:instructors,id',
            ],
            'ambiente_id' => [
                'nullable',
                'integer',
                'exists:ambientes,id',
            ],
            'modalidad_formacion_id' => [
                'nullable',
                'integer',
                'exists:parametros,id',
            ],
            'sede_id' => [
                'nullable',
                'integer',
                'exists:sedes,id',
            ],
            'jornada_id' => [
                'nullable',
                'integer',
                'exists:parametros_temas,id',
            ],
            'total_horas' => [
                'nullable',
                'integer',
                'min:1',
                'max:9999',
            ],
            'status' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
