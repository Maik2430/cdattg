<?php

namespace App\Http\Controllers;

use App\Configuration\UploadLimits;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorApiActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorDashboardActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorDashboardCalendarHelpers;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorDashboardStatsHelpers;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorDestroyActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorEspecialidadActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorEspecialidadAssignActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorEspecialidadRemoveActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorFichaAssignActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorFichaAssignmentActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorFichaAvailabilityActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorFichaStateActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorFichaUnassignActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorFichaValidationActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorFormDataEditHelpers;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorFormDataEditJornadaHelpers;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorFormDataHelpers;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorImportActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorReadActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorSearchActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorStoreDataHelpers;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorUpdateActions;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorUpdateEspecialidadJornadaHelpers;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorUpdateModalidadJsonHelpers;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorUpdateSyncHelpers;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorUpdateValidationHelpers;
use App\Http\Controllers\Concerns\Instructor\HandlesInstructorWriteActions;
use App\Services\InstructorBusinessRulesService;

class InstructorController extends Controller
{
    use HandlesInstructorApiActions;
    use HandlesInstructorDashboardActions;
    use HandlesInstructorDashboardCalendarHelpers;
    use HandlesInstructorDashboardStatsHelpers;
    use HandlesInstructorDestroyActions;
    use HandlesInstructorEspecialidadActions;
    use HandlesInstructorEspecialidadAssignActions;
    use HandlesInstructorEspecialidadRemoveActions;
    use HandlesInstructorFichaAssignActions;
    use HandlesInstructorFichaAssignmentActions;
    use HandlesInstructorFichaAvailabilityActions;
    use HandlesInstructorFichaStateActions;
    use HandlesInstructorFichaUnassignActions;
    use HandlesInstructorFichaValidationActions;
    use HandlesInstructorFormDataEditHelpers;
    use HandlesInstructorFormDataEditJornadaHelpers;
    use HandlesInstructorFormDataHelpers;
    use HandlesInstructorImportActions;
    use HandlesInstructorReadActions;
    use HandlesInstructorSearchActions;
    use HandlesInstructorStoreDataHelpers;
    use HandlesInstructorUpdateActions;
    use HandlesInstructorUpdateEspecialidadJornadaHelpers;
    use HandlesInstructorUpdateModalidadJsonHelpers;
    use HandlesInstructorUpdateSyncHelpers;
    use HandlesInstructorUpdateValidationHelpers;
    use HandlesInstructorWriteActions;

    private const MAX_IMPORT_FILE_KB = UploadLimits::IMPORT_FILE_SIZE_KB;

    private const CSV_HEADER_EMAIL = 'CORREO INSTITUCIONAL';

    protected $businessRulesService;

    protected $instructorService;

    public function __construct(
        InstructorBusinessRulesService $businessRulesService,
        \App\Services\InstructorService $instructorService
    ) {
        $this->middleware('auth'); // Middleware de autenticación para todos los métodos del controlador
        $this->businessRulesService = $businessRulesService;
        $this->instructorService = $instructorService;

        // Middleware específico para métodos individuales usando permisos de Instructor
        // Temporalmente comentado para debuggear
        // $this->middleware('can:VER INSTRUCTOR')->only(['index', 'show']);
        // $this->middleware('can:CREAR INSTRUCTOR')->only(['create', 'store']);
        $this->middleware('can:EDITAR INSTRUCTOR')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR INSTRUCTOR')->only('destroy');
        $this->middleware('can:GESTIONAR ESPECIALIDADES INSTRUCTOR')->only(['especialidades', 'asignarEspecialidad']);
        $this->middleware('can:VER FICHAS ASIGNADAS')->only('fichasAsignadas');
        $this->middleware('can:CAMBIAR ESTADO INSTRUCTOR')->only('cambiarEstado');
        $this->middleware('validate.content.length:'.UploadLimits::IMPORT_CONTENT_LENGTH_BYTES)
            ->only('storeImportarCSV');
    }
}
