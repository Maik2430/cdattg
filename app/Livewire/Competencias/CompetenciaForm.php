<?php

namespace App\Livewire\Competencias;

use Livewire\Component;
use App\Models\Competencia;

class CompetenciaForm extends Component
{
    public $codigo;
    public $nombre;
    public $descripcion;
    public $duracion;
    public $fecha_inicio;
    public $fecha_fin;
    public $status = true;
    
    public $isEdit = false;
    public $competenciaId;

    protected $rules = [
        'codigo' => 'required|string|max:20|unique:competencias,codigo',
        'nombre' => 'required|string|max:255',
        'descripcion' => 'required|string|max:500',
        'duracion' => 'required|numeric|min:0|max:9999.99',
        'fecha_inicio' => 'nullable|date|before_or_equal:fecha_fin',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        'status' => 'boolean',
    ];

    protected $messages = [
        'codigo.required' => 'El código es obligatorio',
        'codigo.unique' => 'Este código ya está registrado',
        'nombre.required' => 'El nombre es obligatorio',
        'descripcion.required' => 'La descripción es obligatoria',
        'duracion.required' => 'La duración es obligatoria',
        'duracion.numeric' => 'La duración debe ser un número',
        'duracion.min' => 'La duración no puede ser negativa',
        'duracion.max' => 'La duración máxima es 9999.99 horas',
        'fecha_inicio.before_or_equal' => 'La fecha de inicio debe ser anterior o igual a la fecha fin',
        'fecha_fin.after_or_equal' => 'La fecha fin debe ser posterior o igual a la fecha de inicio',
    ];

    public function mount()
    {
        $this->status = true;
    }

    public function loadCompetencia($competenciaId)
    {
        $this->isEdit = true;
        $this->competenciaId = $competenciaId;
        
        $competencia = Competencia::find($competenciaId);
        $this->codigo = $competencia->codigo;
        $this->nombre = $competencia->nombre;
        $this->descripcion = $competencia->descripcion;
        $this->duracion = $competencia->duracion;
        $this->fecha_inicio = $competencia->fecha_inicio?->format('Y-m-d');
        $this->fecha_fin = $competencia->fecha_fin?->format('Y-m-d');
        $this->status = $competencia->status;
    }

    public function save()
    {
        if ($this->isEdit) {
            $this->rules['codigo'] = 'required|string|max:20|unique:competencias,codigo,' . $this->competenciaId;
        }

        $this->validate();

        try {
            if ($this->isEdit) {
                // Actualizar
                $competencia = Competencia::find($this->competenciaId);
                $competencia->update([
                    'codigo' => strtoupper($this->codigo),
                    'nombre' => $this->nombre,
                    'descripcion' => $this->descripcion,
                    'duracion' => $this->duracion,
                    'fecha_inicio' => $this->fecha_inicio,
                    'fecha_fin' => $this->fecha_fin,
                    'status' => $this->status,
                    'user_edit_id' => auth()->id(),
                ]);

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Competencia actualizada correctamente'
                ]);
                $this->dispatch('competenciaActualizada');
            } else {
                // Crear
                $competencia = Competencia::create([
                    'codigo' => strtoupper($this->codigo),
                    'nombre' => $this->nombre,
                    'descripcion' => $this->descripcion,
                    'duracion' => $this->duracion,
                    'fecha_inicio' => $this->fecha_inicio,
                    'fecha_fin' => $this->fecha_fin,
                    'status' => $this->status,
                    'user_create_id' => auth()->id(),
                ]);

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Competencia creada correctamente'
                ]);
                $this->dispatch('competenciaCreada');
            }

            $this->cancel();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al guardar la competencia: ' . $e->getMessage()
            ]);
        }
    }

    public function cancel()
    {
        $this->reset();
        $this->resetValidation();
        $this->mount();
        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.competencias.competencia-form');
    }
}
