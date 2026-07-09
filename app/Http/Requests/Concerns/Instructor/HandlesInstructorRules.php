<?php

namespace App\Http\Requests\Concerns\Instructor;

use Illuminate\Validation\Rule;

trait HandlesInstructorRules
{
    public function rules(): array
    {
        $instructorId = $this->route('instructor');

        return [
            'persona_id' => [
                'required',
                'integer',
                'exists:personas,id',
                Rule::unique('instructors')->ignore($instructorId),
            ],
            'regional_id' => 'required|integer|exists:regionals,id',
            'status' => 'boolean',
            'especialidades' => 'nullable|array',
            'especialidades.principal' => 'nullable|string|max:255',
            'especialidades.secundarias' => 'nullable|array',
            'especialidades.secundarias.*' => 'string|max:255',
            'competencias' => 'nullable|array',
            'competencias.*' => 'string|max:255',
            'anos_experiencia' => 'nullable|integer|min:0|max:50',
            'experiencia_laboral' => 'nullable|string|max:1000',
        ];
    }
}
