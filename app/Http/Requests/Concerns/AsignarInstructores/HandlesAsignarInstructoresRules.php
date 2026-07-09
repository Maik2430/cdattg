<?php

namespace App\Http\Requests\Concerns\AsignarInstructores;

trait HandlesAsignarInstructoresRules
{
    public function rules(): array
    {
        return [
            'instructores' => 'required|array|min:1|max:10',
            'instructores.*.instructor_id' => [
                'required',
                'integer',
                'exists:instructors,id',
                function ($attribute, $value, $fail) {
                    $this->validarInstructorActivo($value, $fail);
                    $this->validarLimiteFichasActivas($value, $fail);
                },
            ],
            'instructores.*.fecha_inicio' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    $this->validarFechaInicioFicha($value, $fail);
                },
            ],
            'instructores.*.fecha_fin' => [
                'required',
                'date',
                'after_or_equal:instructores.*.fecha_inicio',
                function ($attribute, $value, $fail) {
                    $this->validarFechaFinFicha($value, $fail);
                },
            ],
            'instructores.*.total_horas_instructor' => 'nullable|integer|min:1|max:1000',
            'instructores.*.competencia_id' => [
                'nullable',
                'integer',
                'exists:competencias,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $this->validarCompetenciaPerteneceAPrograma($value, $fail, $attribute);
                    }
                },
            ],
            'instructores.*.resultados_aprendizaje' => 'nullable|array',
            'instructores.*.resultados_aprendizaje.*' => [
                'required',
                'integer',
                'exists:resultados_aprendizajes,id',
                function ($attribute, $value, $fail) {
                    $this->validarResultadoPerteneceACompetencia($value, $fail, $attribute);
                },
            ],
            'instructores.*.dias_semana' => 'required|array|min:1|max:7',
            'instructores.*.dias_semana.*' => 'required|integer|exists:parametros_temas,id',
            'instructores.*.dias' => 'nullable|array',
            'instructores.*.dias.*.hora_inicio' => 'required_with:instructores.*.dias|date_format:H:i',
            'instructores.*.dias.*.hora_fin' => 'required_with:instructores.*.dias|date_format:H:i|after:instructores.*.dias.*.hora_inicio',
            'instructores.*.dias_formacion' => 'nullable|array|min:1|max:7',
            'instructores.*.dias_formacion.*.dia_id' => 'exists:parametros_temas,id',
            'instructor_principal_id' => [
                'nullable',
                'integer',
                'exists:instructors,id',
            ],
        ];
    }
}
