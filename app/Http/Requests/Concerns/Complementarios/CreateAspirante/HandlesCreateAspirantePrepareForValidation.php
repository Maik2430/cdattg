<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns\Complementarios\CreateAspirante;

trait HandlesCreateAspirantePrepareForValidation
{
    protected function prepareForValidation(): void
    {
        if ($this->has('tipo_documento') && ! $this->has('tipo_documento_id')) {
            $this->merge(['tipo_documento_id' => $this->tipo_documento]);
        }

        $this->trimFields(['numero_documento', 'primer_nombre', 'primer_apellido']);
        $this->trimFields(['segundo_nombre', 'segundo_apellido', 'direccion', 'observaciones'], true);
        $this->trimField('email', true, fn (string $value): string => strtolower(trim($value)));

        if ($this->has('caracterizacion_ids') && ! $this->has('caracterizaciones')) {
            $this->merge(['caracterizaciones' => $this->caracterizacion_ids ?? []]);
        }

        if ($this->has('genero') && ! $this->has('genero_id')) {
            $this->merge(['genero_id' => $this->genero]);
        }
    }
}
