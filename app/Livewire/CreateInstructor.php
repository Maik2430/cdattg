<?php

namespace App\Livewire;

use App\Livewire\Concerns\CreateInstructor\HandlesCreateInstructorDynamicFieldActions;
use App\Livewire\Concerns\CreateInstructor\HandlesCreateInstructorMountHelpers;
use App\Livewire\Concerns\CreateInstructor\HandlesCreateInstructorRenderActions;
use App\Livewire\Concerns\CreateInstructor\HandlesCreateInstructorStoreActions;
use App\Livewire\Concerns\CreateInstructor\HandlesCreateInstructorValidationHelpers;
use App\Services\InstructorService;
use Livewire\Component;

class CreateInstructor extends Component
{
    use HandlesCreateInstructorDynamicFieldActions;
    use HandlesCreateInstructorMountHelpers;
    use HandlesCreateInstructorRenderActions;
    use HandlesCreateInstructorStoreActions;
    use HandlesCreateInstructorValidationHelpers;

    // Selección de persona
    public $persona_id = null;

    // Información laboral
    public $regional_id = null;

    public $centro_formacion_id = null;

    public $tipo_vinculacion_id = null;

    public $jornadas = [];

    public $fecha_ingreso_sena = null;

    public $anos_experiencia = null;

    public $experiencia_instructor_meses = null;

    public $experiencia_laboral = null;

    // Formación académica
    public $nivel_academico_id = null;

    public $formacion_pedagogia = null;

    public $titulos_obtenidos = [''];

    public $instituciones_educativas = [''];

    public $certificaciones_tecnicas = [''];

    public $cursos_complementarios = [''];

    // Competencias y habilidades
    public $areas_experticia = [''];

    public $competencias_tic = [''];

    public $idiomas = [['idioma' => '', 'nivel' => '']];

    public $modalidades = [];

    public $especialidades = [];

    // Información administrativa
    public $numero_contrato = null;

    public $fecha_inicio_contrato = null;

    public $fecha_fin_contrato = null;

    public $supervisor_contrato = null;

    public $eps = null;

    public $arl = null;

    // Datos para selects
    public $centrosFormacion = [];

    protected InstructorService $instructorService;

    public function boot(InstructorService $instructorService): void
    {
        $this->instructorService = $instructorService;
    }
}
