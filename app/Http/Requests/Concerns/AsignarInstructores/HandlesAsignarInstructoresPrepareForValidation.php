<?php

namespace App\Http\Requests\Concerns\AsignarInstructores;

use App\Models\FichaCaracterizacion;

trait HandlesAsignarInstructoresPrepareForValidation
{
    protected function prepareForValidation(): void
    {
        if (! $this->has('instructor_principal_id') || ! $this->input('instructor_principal_id')) {
            $fichaId = $this->route('id');
            $ficha = FichaCaracterizacion::find($fichaId);

            if ($ficha && $ficha->instructor_id) {
                $this->merge([
                    'instructor_principal_id' => $ficha->instructor_id,
                ]);
            }
        }
    }
}
