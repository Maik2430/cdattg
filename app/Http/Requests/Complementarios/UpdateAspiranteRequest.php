<?php

declare(strict_types=1);

namespace App\Http\Requests\Complementarios;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAspiranteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $aspiranteId = $this->route('aspirante') ?? $this->route('aspiranteId');

        return [
            'estado' => [
                'sometimes',
                'required',
                'integer',
                Rule::in([1, 3, 4]), // 1=En proceso, 3=Admitido, 4=Rechazado
            ],
            'observaciones' => [
                'sometimes',
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'estado.required' => 'El estado es obligatorio.',
            'estado.integer' => 'El estado debe ser un número entero.',
            'estado.in' => 'El estado debe ser: 1 (En proceso), 3 (Admitido) o 4 (Rechazado).',
            'observaciones.string' => 'Las observaciones deben ser una cadena de texto.',
            'observaciones.max' => 'Las observaciones no pueden exceder los 500 caracteres.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('observaciones')) {
            $this->merge([
                'observaciones' => $this->observaciones ? trim($this->observaciones) : null,
            ]);
        }
    }
}

