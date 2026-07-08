<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaAmbienteActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaApiEstadisticasActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaApiGetAllActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaApiGetByIdActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaApiJornadaActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaApiJornadaFormatHelpers;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaApiMiscActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaApiSearchActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaApiSearchFormatHelpers;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaAprendicesAssignActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaAprendicesDesassignActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaAprendicesDesassignIndividualHelpers;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaAprendicesDesassignTransactionHelpers;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaAprendicesGestionActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaAsistenciaHelpers;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaCrudEditUpdateActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaCrudReadActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaCrudStoreActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaDestroyActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaDestroyAuthValidationHelpers;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaDestroyExecutionHelpers;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaDiasFormacionDeleteActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaDiasFormacionHelpers;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaDiasFormacionReadActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaDiasFormacionStoreActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaDiasFormacionUpdateActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaEstadoActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaFormDataHelpers;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaInstructorAsignacionUpdateActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaInstructorAsignacionValidationHelpers;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaInstructorAssignActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaInstructorAvailabilityHelpers;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaInstructorCompetenciaReadActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaInstructorCompetenciaWriteActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaInstructorDiasReadActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaInstructorDiasWriteActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaInstructorDisponiblesActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaInstructorFechasActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaInstructorGestionActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaInstructorGestionHelpers;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaInstructorPreviewActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaSearchActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaSearchQueryHelpers;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaValidationCompletaActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaValidationDisponibilidadActions;
use App\Http\Controllers\Concerns\FichaCaracterizacion\HandlesFichaValidationEdicionActions;
use App\Repositories\ConfiguracionRepository;
use App\Services\FichaCaracterizacionValidationService;
use App\Services\FichaService;

class FichaCaracterizacionController extends Controller
{
    use HandlesFichaAmbienteActions;
    use HandlesFichaApiEstadisticasActions;
    use HandlesFichaApiGetAllActions;
    use HandlesFichaApiGetByIdActions;
    use HandlesFichaApiJornadaActions;
    use HandlesFichaApiJornadaFormatHelpers;
    use HandlesFichaApiMiscActions;
    use HandlesFichaApiSearchActions;
    use HandlesFichaApiSearchFormatHelpers;
    use HandlesFichaAprendicesAssignActions;
    use HandlesFichaAprendicesDesassignActions;
    use HandlesFichaAprendicesDesassignIndividualHelpers;
    use HandlesFichaAprendicesDesassignTransactionHelpers;
    use HandlesFichaAprendicesGestionActions;
    use HandlesFichaAsistenciaHelpers;
    use HandlesFichaCrudEditUpdateActions;
    use HandlesFichaCrudReadActions;
    use HandlesFichaCrudStoreActions;
    use HandlesFichaDestroyActions;
    use HandlesFichaDestroyAuthValidationHelpers;
    use HandlesFichaDestroyExecutionHelpers;
    use HandlesFichaDiasFormacionDeleteActions;
    use HandlesFichaDiasFormacionHelpers;
    use HandlesFichaDiasFormacionReadActions;
    use HandlesFichaDiasFormacionStoreActions;
    use HandlesFichaDiasFormacionUpdateActions;
    use HandlesFichaEstadoActions;
    use HandlesFichaFormDataHelpers;
    use HandlesFichaInstructorAsignacionUpdateActions;
    use HandlesFichaInstructorAsignacionValidationHelpers;
    use HandlesFichaInstructorAssignActions;
    use HandlesFichaInstructorAvailabilityHelpers;
    use HandlesFichaInstructorCompetenciaReadActions;
    use HandlesFichaInstructorCompetenciaWriteActions;
    use HandlesFichaInstructorDiasReadActions;
    use HandlesFichaInstructorDiasWriteActions;
    use HandlesFichaInstructorDisponiblesActions;
    use HandlesFichaInstructorFechasActions;
    use HandlesFichaInstructorGestionActions;
    use HandlesFichaInstructorGestionHelpers;
    use HandlesFichaInstructorPreviewActions;
    use HandlesFichaSearchActions;
    use HandlesFichaSearchQueryHelpers;
    use HandlesFichaValidationCompletaActions;
    use HandlesFichaValidationDisponibilidadActions;
    use HandlesFichaValidationEdicionActions;

    protected FichaService $fichaService;

    protected FichaCaracterizacionValidationService $validationService;

    protected ConfiguracionRepository $configuracionRepo;

    public function __construct(
        FichaService $fichaService,
        FichaCaracterizacionValidationService $validationService,
        ConfiguracionRepository $configuracionRepo
    ) {
        $this->middleware('auth');
        $this->fichaService = $fichaService;
        $this->validationService = $validationService;
        $this->configuracionRepo = $configuracionRepo;

        $this->middleware('can:VER FICHA CARACTERIZACION')->only(['index', 'show', 'create', 'edit']);
        $this->middleware('can:CREAR FICHA CARACTERIZACION')->only(['store']);
        $this->middleware('can:EDITAR FICHA CARACTERIZACION')->only(['update']);
        $this->middleware('can:ELIMINAR FICHA CARACTERIZACION')->only(['destroy']);
    }
}
