<?php

namespace App\Livewire\Instructores;

use App\Livewire\Instructores\Concerns\InstructorForm\HandlesInstructorFormComputedProperties;
use App\Livewire\Instructores\Concerns\InstructorForm\HandlesInstructorFormDynamicFieldActions;
use App\Livewire\Instructores\Concerns\InstructorForm\HandlesInstructorFormEspecialidadesActions;
use App\Livewire\Instructores\Concerns\InstructorForm\HandlesInstructorFormLoadDataHelpers;
use App\Livewire\Instructores\Concerns\InstructorForm\HandlesInstructorFormMountHelpers;
use App\Livewire\Instructores\Concerns\InstructorForm\HandlesInstructorFormRenderActions;
use App\Livewire\Instructores\Concerns\InstructorForm\HandlesInstructorFormSaveActions;
use App\Livewire\Instructores\Concerns\InstructorForm\HandlesInstructorFormSelectDataHelpers;
use App\Livewire\Instructores\Concerns\InstructorForm\HandlesInstructorFormValidationHelpers;
use App\Services\InstructorBusinessRulesService;
use App\Services\InstructorService;
use Livewire\Component;

class InstructorForm extends Component
{
    use HandlesInstructorFormComputedProperties;
    use HandlesInstructorFormDynamicFieldActions;
    use HandlesInstructorFormEspecialidadesActions;
    use HandlesInstructorFormLoadDataHelpers;
    use HandlesInstructorFormMountHelpers;
    use HandlesInstructorFormRenderActions;
    use HandlesInstructorFormSaveActions;
    use HandlesInstructorFormSelectDataHelpers;
    use HandlesInstructorFormValidationHelpers;

    public $isEdit = false;

    public $instructor = null;

    public $persona_id = null;

    public $regional_id = null;

    public $centro_formacion_id = null;

    public $tipo_vinculacion_id = null;

    public $jornadas = [];

    public $fecha_ingreso_sena = null;

    public $anos_experiencia = null;

    public $experiencia_instructor_meses = null;

    public $experiencia_laboral = null;

    public $nivel_academico_id = null;

    public $formacion_pedagogia = null;

    public $titulos_obtenidos = [''];

    public $instituciones_educativas = [''];

    public $certificaciones_tecnicas = [''];

    public $cursos_complementarios = [''];

    public $areas_experticia = [''];

    public $competencias_tic = [''];

    public $idiomas = [['idioma' => '', 'nivel' => '']];

    public $modalidades = [];

    public $especialidades = [];

    public $numero_contrato = null;

    public $fecha_inicio_contrato = null;

    public $fecha_fin_contrato = null;

    public $supervisor_contrato = null;

    public $personasDisponibles = [];

    public $regionales = [];

    public $centrosFormacion = [];

    public $tiposVinculacion = [];

    public $jornadasTrabajo = [];

    public $nivelesAcademicos = [];

    public $redesConocimiento = [];

    public $modalidadesDisponibles = [];

    protected InstructorService $instructorService;

    protected InstructorBusinessRulesService $businessRulesService;

    protected $listeners = [
        'closeModal' => 'handleCloseModal',
        'refreshComponent' => '$refresh',
    ];

    public function boot(
        InstructorService $instructorService,
        InstructorBusinessRulesService $businessRulesService
    ): void {
        $this->instructorService = $instructorService;
        $this->businessRulesService = $businessRulesService;
    }
}
