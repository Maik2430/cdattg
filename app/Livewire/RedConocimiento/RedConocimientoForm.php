<?php

namespace App\Livewire\RedConocimiento;

use Livewire\Component;
use App\Models\RedConocimiento;
use App\Models\Regional;
use Livewire\Attributes\Validate;

class RedConocimientoForm extends Component
{
    public $redId = null;
    public $isEdit = false;

    #[Validate('required|string|max:255')]
    public $nombre = '';

    #[Validate('required|exists:regionals,id')]
    public $regionals_id = '';

    protected $listeners = [
        'editRed' => 'loadRed',
    ];

    public function mount($redId = null)
    {
        if ($redId) {
            $this->isEdit = true;
            $this->loadRed($redId);
        }
    }

    public function loadRed($redId)
    {
        $red = RedConocimiento::find($redId);
        if ($red) {
            $this->redId = $red->id;
            $this->isEdit = true;
            $this->nombre = $red->nombre;
            $this->regionals_id = $red->regionals_id;
        }
    }

    public function save()
    {
        if ($this->isEdit) {
            $this->update();
        } else {
            $this->store();
        }
    }

    public function store()
    {
        try {
            $validated = $this->validate();
            
            // Validación manual de unicidad del nombre
            $existingRed = RedConocimiento::where('nombre', $validated['nombre'])->first();
                
            if ($existingRed) {
                $this->addError('nombre', 'El nombre ya está siendo utilizado por otra red.');
                return;
            }
            
            // FORZAR ESTADO ACTIVO POR DEFECTO
            $validated['status'] = true;
            $validated['user_create_id'] = auth()->id();
            
            $red = RedConocimiento::create($validated);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Red de conocimiento creada correctamente',
            ]);
            
            $this->dispatch('redCreada');
            $this->reset();
            $this->dispatch('closeModal');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al crear la red: ' . $e->getMessage(),
            ]);
        }
    }

    public function update()
    {
        try {
            $validated = $this->validate();
            
            // Validación manual de unicidad del nombre
            $existingRed = RedConocimiento::where('nombre', $validated['nombre'])
                ->where('id', '!=', $this->redId)
                ->first();
                
            if ($existingRed) {
                $this->addError('nombre', 'El nombre ya está siendo utilizado por otra red.');
                return;
            }
            
            $red = RedConocimiento::find($this->redId);
            if (!$red) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Red de conocimiento no encontrada',
                ]);
                return;
            }
            
            // Mantener el estado actual de la red (no forzar a true)
            $validated['user_edit_id'] = auth()->id();
            
            $red->update($validated);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Red de conocimiento actualizada correctamente',
            ]);
            
            $this->dispatch('redActualizada');
            $this->reset();
            $this->dispatch('closeModal');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al actualizar la red: ' . $e->getMessage(),
            ]);
        }
    }

    public function cancel()
    {
        $this->reset();
        $this->dispatch('closeModal');
    }

    public function getRegionalesProperty()
    {
        return Regional::where('status', 1)->get();
    }

    public function render()
    {
        return view('livewire.red-conocimiento.red-conocimiento-form');
    }

    protected function rules()
    {
        $rules = [
            'nombre' => 'required|string|max:255|unique:red_conocimientos,nombre',
            'regionals_id' => 'required|exists:regionals,id',
        ];

        if ($this->isEdit && $this->redId) {
            $rules['nombre'] = 'required|string|max:255|unique:red_conocimientos,nombre,' . $this->redId . ',id';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'nombre.unique' => 'El nombre ya está siendo utilizado por otra red.',
            'nombre.required' => 'El nombre de la red es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
            'regionals_id.required' => 'Debe seleccionar una regional.',
            'regionals_id.exists' => 'La regional seleccionada no es válida.',
        ];
    }
}
