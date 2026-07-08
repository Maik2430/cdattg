<?php

namespace App\Livewire\Fichas;

use App\Livewire\Fichas\Concerns\FichaForm\HandlesFichaFormLoadDataHelpers;
use App\Livewire\Fichas\Concerns\FichaForm\HandlesFichaFormMountHelpers;
use App\Livewire\Fichas\Concerns\FichaForm\HandlesFichaFormRenderActions;
use App\Livewire\Fichas\Concerns\FichaForm\HandlesFichaFormResetActions;
use App\Livewire\Fichas\Concerns\FichaForm\HandlesFichaFormSaveActions;
use App\Livewire\Fichas\Concerns\FichaForm\HandlesFichaFormSelectDataHelpers;
use App\Livewire\Fichas\Concerns\FichaForm\HandlesFichaFormUpdateActions;
use App\Livewire\Fichas\Concerns\FichaForm\HandlesFichaFormValidationHelpers;
use Livewire\Component;

class FichaForm extends Component
{
    use HandlesFichaFormLoadDataHelpers;
    use HandlesFichaFormMountHelpers;
    use HandlesFichaFormRenderActions;
    use HandlesFichaFormResetActions;
    use HandlesFichaFormSaveActions;
    use HandlesFichaFormSelectDataHelpers;
    use HandlesFichaFormUpdateActions;
    use HandlesFichaFormValidationHelpers;

    public $ficha;

    public $isEdit = false;

    public $ficha_codigo;

    public $programa_formacion_id;

    public $sede_id;

    public $instructor_id;

    public $ambiente_id;

    public $fecha_inicio;

    public $fecha_fin;

    public $modalidad_formacion_id;

    public $jornada_id;

    public $total_horas;

    public $dias_formacion = [];

    public $status = 1;

    public $programas;

    public $sedes;

    public $instructores;

    public $ambientes;

    public $modalidades;

    public $jornadas;

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];
}
