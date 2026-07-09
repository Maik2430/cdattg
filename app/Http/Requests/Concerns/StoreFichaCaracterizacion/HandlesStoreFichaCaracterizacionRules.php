<?php

namespace App\Http\Requests\Concerns\StoreFichaCaracterizacion;

trait HandlesStoreFichaCaracterizacionRules
{
    public function rules(): array
    {
        \Log::info('StoreFichaCaracterizacionRequest rules called', [
            'user_id' => $this->user()->id,
            'all_data' => $this->all(),
        ]);

        return [
            'ficha' => [
                'required',
                'string',
                'max:50',
                'unique:fichas_caracterizacion,ficha',
            ],
            'programa_formacion_id' => [
                'required',
                'integer',
                'exists:programas_formacion,id',
            ],
            'fecha_inicio' => [
                'required',
                'date',
                'after_or_equal:'.now()->subYears(2)->format('Y-m-d'),
            ],
            'fecha_fin' => [
                'required',
                'date',
                'after:fecha_inicio',
            ],
            'instructor_id' => [
                'required',
                'integer',
                'exists:instructors,id',
            ],
            'ambiente_id' => [
                'required',
                'integer',
                'exists:ambientes,id',
            ],
            'modalidad_formacion_id' => [
                'required',
                'integer',
                'exists:parametros,id',
            ],
            'sede_id' => [
                'required',
                'integer',
                'exists:sedes,id',
            ],
            'jornada_id' => [
                'required',
                'integer',
                'exists:parametros_temas,id',
            ],
            'total_horas' => [
                'required',
                'integer',
                'min:1',
                'max:9999',
            ],
            'status' => [
                'required',
                'boolean',
            ],
            'dias_formacion' => [
                'required',
                'array',
                'min:1',
            ],
            'dias_formacion.*' => [
                'required',
                'integer',
                'in:12,13,14,15,16,17,18',
            ],
            'horarios' => [
                'nullable',
                'array',
            ],
            'horarios.*' => [
                'nullable',
                'array',
            ],
            'horarios.*.hora_inicio' => [
                'nullable',
                'date_format:H:i',
            ],
            'horarios.*.hora_fin' => [
                'nullable',
                'date_format:H:i',
                'after:horarios.*.hora_inicio',
            ],
        ];
    }
}
