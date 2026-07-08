<?php

namespace App\Livewire\Aprendices\Concerns;

use App\Models\FichaCaracterizacion;
use App\Models\Persona;

trait HandlesAprendizFormSelectDataHelpers
{
    private function cargarDatosSelects()
    {
        $this->fichas = FichaCaracterizacion::with(['programaFormacion'])
            ->where('status', true)
            ->orderBy('ficha')
            ->get();

        if ($this->isEdit && $this->aprendiz && $this->aprendiz->persona) {
            $this->personas = collect([$this->aprendiz->persona])
                ->map(fn ($persona) => $this->mapPersonaNombreCompleto($persona));
        } else {
            $this->personas = Persona::whereDoesntHave('aprendiz')
                ->select('id', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'numero_documento')
                ->get()
                ->map(fn ($persona) => $this->mapPersonaNombreCompleto($persona));

            if ($this->personas->count() === 0) {
                $this->personas = Persona::select('id', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'numero_documento')
                    ->limit(10)
                    ->get()
                    ->map(fn ($persona) => $this->mapPersonaNombreCompleto($persona));
            }
        }
    }

    private function mapPersonaNombreCompleto($persona)
    {
        $persona->nombre_completo = trim($persona->primer_nombre.' '.
            ($persona->segundo_nombre ?? '').' '.
            $persona->primer_apellido.' '.
            ($persona->segundo_apellido ?? ''));

        return $persona;
    }
}
