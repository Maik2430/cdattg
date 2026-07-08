<?php

namespace App\Livewire\Aprendices\Concerns;

trait HandlesAprendizFormRenderActions
{
    public function updatedDepartamentoId()
    {
        if ($this->departamento_id) {
            $this->municipios = collect([
                ['id' => 1, 'departamento_id' => 1, 'nombre' => 'Medellín'],
                ['id' => 2, 'departamento_id' => 1, 'nombre' => 'Envigado'],
                ['id' => 3, 'departamento_id' => 2, 'nombre' => 'Cali'],
                ['id' => 4, 'departamento_id' => 3, 'nombre' => 'Bogotá'],
            ])->where('departamento_id', $this->departamento_id);
        } else {
            $this->municipios = collect([]);
        }

        $this->municipio_id = null;
    }

    public function cancel()
    {
        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.aprendices.aprendiz-form');
    }

    private function prepareDataForService()
    {
        return [
            'ficha_caracterizacion_id' => $this->ficha_caracterizacion_id,
            'estado' => $this->estado,
            'persona' => [
                'tipo_documento_id' => $this->tipo_documento_id,
                'numero_documento' => $this->numero_documento,
                'primer_nombre' => $this->primer_nombre,
                'segundo_nombre' => $this->segundo_nombre,
                'primer_apellido' => $this->primer_apellido,
                'segundo_apellido' => $this->segundo_apellido,
                'email' => $this->email,
                'telefono' => $this->telefono,
                'direccion' => $this->direccion,
                'barrio' => $this->barrio,
                'municipio_id' => $this->municipio_id,
            ],
        ];
    }
}
