<?php

namespace App\Livewire\Asistencia;

use App\Livewire\Asistencia\Concerns\HandlesCrearEvidenciaModalOpenActions;
use App\Livewire\Asistencia\Concerns\HandlesCrearEvidenciaModalRenderActions;
use App\Livewire\Asistencia\Concerns\HandlesCrearEvidenciaModalStoreActions;
use Livewire\Component;

class CrearEvidenciaModal extends Component
{
    use HandlesCrearEvidenciaModalOpenActions;
    use HandlesCrearEvidenciaModalRenderActions;
    use HandlesCrearEvidenciaModalStoreActions;

    public $showModalEvidencia = false;

    public $nombreEvidencia = '';

    public $selectedFicha = null;

    public $selectedFichaId = null;

    protected $rules = [
        'nombreEvidencia' => 'required|min:3|max:255',
    ];

    protected $messages = [
        'nombreEvidencia.required' => 'El nombre de la evidencia es obligatorio.',
        'nombreEvidencia.min' => 'El nombre debe tener al menos 3 caracteres.',
        'nombreEvidencia.max' => 'El nombre no puede superar 255 caracteres.',
    ];

    protected $listeners = ['openModalEvidencia'];
}
