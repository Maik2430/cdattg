<?php

namespace App\Http\Requests\Concerns\CreateInstructor;

use App\Models\ParametroTema;
use App\Models\Persona;

trait HandlesCreateInstructorWithValidator
{
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validarJornadasTema($validator);
            $this->validarPersonaDisponible($validator);
        });
    }

    private function validarJornadasTema($validator): void
    {
        if (! $this->has('jornadas') || ! is_array($this->jornadas)) {
            return;
        }

        $jornadasInvalidas = [];
        foreach ($this->jornadas as $jornadaId) {
            $jornadaParametroTema = ParametroTema::where('id', $jornadaId)
                ->whereHas('tema', function ($q) {
                    $q->where('name', 'LIKE', '%JORNADA%');
                })
                ->first();

            if (! $jornadaParametroTema) {
                $jornadasInvalidas[] = $jornadaId;
            }
        }

        if (! empty($jornadasInvalidas)) {
            $validator->errors()->add('jornadas', 'Una o más jornadas seleccionadas no pertenecen al tema JORNADA.');
        }
    }

    private function validarPersonaDisponible($validator): void
    {
        if (! $this->has('persona_id')) {
            return;
        }

        $persona = Persona::with(['instructor', 'user'])->find($this->input('persona_id'));

        if (! $persona) {
            $validator->errors()->add('persona_id', 'La persona seleccionada no existe.');

            return;
        }

        if ($persona->instructor) {
            $validator->errors()->add('persona_id', 'Esta persona ya es instructor.');
        }

        if (! $persona->user) {
            $validator->errors()->add('persona_id', 'Esta persona no tiene un usuario asociado.');
        }
    }
}
