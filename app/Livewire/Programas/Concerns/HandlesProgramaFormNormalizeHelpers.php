<?php

namespace App\Livewire\Programas\Concerns;

use App\Models\ProgramaFormacion;

trait HandlesProgramaFormNormalizeHelpers
{
    protected function normalizeProgramaFormFields(): void
    {
        $this->codigo = (string) $this->codigo;

        foreach ([
            'red_conocimiento_id',
            'nivel_formacion_id',
            'horas_totales',
            'horas_etapa_lectiva',
            'horas_etapa_productiva',
        ] as $field) {
            if ($this->$field === '') {
                $this->$field = null;
            }
        }
    }

    protected function programaCodigoDuplicado(string $codigo, ?int $excludeId = null): bool
    {
        $query = ProgramaFormacion::where('codigo', $codigo);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
