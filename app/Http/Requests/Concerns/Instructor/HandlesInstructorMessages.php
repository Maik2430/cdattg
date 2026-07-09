<?php

namespace App\Http\Requests\Concerns\Instructor;

trait HandlesInstructorMessages
{
    public function messages(): array
    {
        return [
            'persona_id.required' => 'La persona es obligatoria.',
            'persona_id.unique' => 'Esta persona ya está registrada como instructor.',
            'persona_id.exists' => 'La persona seleccionada no existe.',
            'regional_id.required' => 'La regional es obligatoria.',
            'regional_id.exists' => 'La regional seleccionada no existe.',
            'especialidades.array' => 'Las especialidades deben ser un arreglo.',
            'especialidades.principal.string' => 'La especialidad principal debe ser texto.',
            'especialidades.secundarias.array' => 'Las especialidades secundarias deben ser un arreglo.',
            'especialidades.secundarias.*.string' => 'Cada especialidad secundaria debe ser texto.',
            'competencias.array' => 'Las competencias deben ser un arreglo.',
            'competencias.*.string' => 'Cada competencia debe ser texto.',
            'anos_experiencia.integer' => 'Los años de experiencia deben ser un número entero.',
            'anos_experiencia.min' => 'Los años de experiencia no pueden ser negativos.',
            'anos_experiencia.max' => 'Los años de experiencia no pueden exceder 50 años.',
            'experiencia_laboral.string' => 'La experiencia laboral debe ser texto.',
            'experiencia_laboral.max' => 'La experiencia laboral no puede exceder 1000 caracteres.',
        ];
    }
}
