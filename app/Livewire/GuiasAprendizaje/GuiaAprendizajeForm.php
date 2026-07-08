<?php

namespace App\Livewire\GuiasAprendizaje;

use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeFormModalActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeFormMountHelpers;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeFormRenderActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeFormResultadosLoadHelpers;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeFormResultadosSelectionActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeFormSaveActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeFormStoreActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeFormUpdateActions;
use App\Livewire\GuiasAprendizaje\Concerns\HandlesGuiaAprendizajeFormValidationHelpers;
use Livewire\Attributes\Validate;
use Livewire\Component;

class GuiaAprendizajeForm extends Component
{
    use HandlesGuiaAprendizajeFormModalActions;
    use HandlesGuiaAprendizajeFormMountHelpers;
    use HandlesGuiaAprendizajeFormRenderActions;
    use HandlesGuiaAprendizajeFormResultadosLoadHelpers;
    use HandlesGuiaAprendizajeFormResultadosSelectionActions;
    use HandlesGuiaAprendizajeFormSaveActions;
    use HandlesGuiaAprendizajeFormStoreActions;
    use HandlesGuiaAprendizajeFormUpdateActions;
    use HandlesGuiaAprendizajeFormValidationHelpers;

    public $guia;

    public $isEdit = false;

    #[Validate('required|string|max:20|unique:guia_aprendizajes,codigo')]
    public $codigo = '';

    #[Validate('required|string|max:255')]
    public $nombre = '';

    #[Validate('required|string|max:1000')]
    public $descripcion = '';

    #[Validate('required|exists:programas_formacion,id')]
    public $programa_formacion_id = '';

    #[Validate('required|integer|min:1|max:999')]
    public $duracion_horas = 40;

    #[Validate('required|integer|min:1|max:12')]
    public $duracion_meses = 1;

    #[Validate('nullable|string|max:500')]
    public $objetivo_general = '';

    #[Validate('nullable|string|max:1000')]
    public $metodologia = '';

    #[Validate('nullable|string|max:1000')]
    public $evaluacion = '';

    #[Validate('boolean')]
    public $status = true;

    public $resultadosSeleccionados = [];

    public $resultadosDisponibles = [];

    public $resultadosAprendizaje = [];

    public $searchResultado = '';

    public $selectAll = false;

    protected $listeners = [
        'closeModal' => 'handleCloseModal',
        'refreshComponent' => '$refresh',
    ];
}
