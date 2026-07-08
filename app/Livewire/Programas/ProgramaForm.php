<?php

namespace App\Livewire\Programas;

use App\Livewire\Programas\Concerns\HandlesProgramaFormMountHelpers;
use App\Livewire\Programas\Concerns\HandlesProgramaFormRenderActions;
use App\Livewire\Programas\Concerns\HandlesProgramaFormSaveActions;
use App\Livewire\Programas\Concerns\HandlesProgramaFormStoreActions;
use App\Livewire\Programas\Concerns\HandlesProgramaFormUpdateActions;
use App\Livewire\Programas\Concerns\HandlesProgramaFormValidationHelpers;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ProgramaForm extends Component
{
    use HandlesProgramaFormMountHelpers;
    use HandlesProgramaFormRenderActions;
    use HandlesProgramaFormSaveActions;
    use HandlesProgramaFormStoreActions;
    use HandlesProgramaFormUpdateActions;
    use HandlesProgramaFormValidationHelpers;

    public $programaId = null;

    public $isEdit = false;

    #[Validate('required|string|max:6')]
    public $codigo = '';

    #[Validate('required|string|max:255')]
    public $nombre = '';

    #[Validate('nullable|exists:red_conocimientos,id')]
    public $red_conocimiento_id = '';

    #[Validate('required|exists:parametros,id')]
    public $nivel_formacion_id = '';

    #[Validate('nullable|integer|min:1')]
    public $horas_totales = '';

    #[Validate('nullable|integer|min:1')]
    public $horas_etapa_lectiva = '';

    #[Validate('nullable|integer|min:1')]
    public $horas_etapa_productiva = '';

    public bool $horas_validas = false;

    protected $listeners = [
        'editPrograma' => 'loadPrograma',
    ];
}
