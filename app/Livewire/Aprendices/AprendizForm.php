<?php

namespace App\Livewire\Aprendices;

use App\Livewire\Aprendices\Concerns\HandlesAprendizFormMountHelpers;
use App\Livewire\Aprendices\Concerns\HandlesAprendizFormRenderActions;
use App\Livewire\Aprendices\Concerns\HandlesAprendizFormSaveActions;
use App\Livewire\Aprendices\Concerns\HandlesAprendizFormSelectDataHelpers;
use App\Livewire\Aprendices\Concerns\HandlesAprendizFormValidationHelpers;
use App\Models\Aprendiz;
use Illuminate\Support\Collection;
use Livewire\Component;

class AprendizForm extends Component
{
    use HandlesAprendizFormMountHelpers;
    use HandlesAprendizFormRenderActions;
    use HandlesAprendizFormSaveActions;
    use HandlesAprendizFormSelectDataHelpers;
    use HandlesAprendizFormValidationHelpers;

    public ?Aprendiz $aprendiz = null;

    public bool $isEdit = false;

    public ?int $persona_id = null;

    public ?int $ficha_caracterizacion_id = null;

    public int $estado = 1;

    public Collection $fichas;

    public Collection $personas;

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];
}
