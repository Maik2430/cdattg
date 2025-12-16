<?php

namespace App\Http\Requests\Complementarios;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramaComplementarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'catalogo_id' => 'nullable|exists:complementarios_catalogo,id',
            'codigo' => 'required_without:catalogo_id|string|unique:complementarios_ofertados,codigo',
            'nombre' => 'required_without:catalogo_id|string',
            'justificacion' => 'required|string|max:600',
            'requisitos_ingreso' => 'required_without:catalogo_id|string|max:400',
            'duracion' => 'required_without:catalogo_id|integer|min:1',
            'cupos' => 'required|integer|min:1',
            'estado' => 'required|integer|in:0,1,2',
            'modalidad_id' => 'nullable|exists:parametros_temas,id',
            'jornada_id' => 'required|exists:jornadas_formacion,id',
            'ambiente_id' => 'required|exists:ambientes,id',
            'dias' => 'nullable|array',
            'dias.*.dia_id' => 'required_with:dias.*.hora_inicio,dias.*.hora_fin|exists:parametros_temas,id',
            'dias.*.hora_inicio' => 'nullable|date_format:H:i',
            'dias.*.hora_fin' => 'nullable|date_format:H:i|after:dias.*.hora_inicio',
            'competencias' => 'nullable|array',
            'competencias.*' => 'exists:competencias,id',
            'raps' => 'nullable|array',
            'raps.*' => 'exists:resultados_aprendizajes,id',
            'guias' => 'nullable|array',
            'guias.*' => 'exists:guia_aprendizajes,id',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        if (isset($validated['dias']) && is_array($validated['dias'])) {
            $validated['dias'] = collect($validated['dias'])
                ->filter(static function ($dia) {
                    return isset($dia['dia_id']);
                })
                ->map(static function ($dia) {
                    return [
                        'dia_id' => (int) $dia['dia_id'],
                        'hora_inicio' => $dia['hora_inicio'] ?? null,
                        'hora_fin' => $dia['hora_fin'] ?? null,
                    ];
                })
                ->values()
                ->all();
        }

        return $validated;
    }
}

