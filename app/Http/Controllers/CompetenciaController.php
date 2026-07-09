<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Competencia\HandlesCompetenciaCrudReadActions;
use App\Http\Controllers\Concerns\Competencia\HandlesCompetenciaCrudWriteActions;
use App\Http\Controllers\Concerns\Competencia\HandlesCompetenciaEstadoActions;
use App\Http\Controllers\Concerns\Competencia\HandlesCompetenciaResultadosActions;
use App\Http\Controllers\Concerns\Competencia\HandlesCompetenciaUpdateActions;
use App\Repositories\CompetenciaRepository;
use App\Repositories\ResultadosAprendizajeRepository;
use App\Services\CompetenciaService;

class CompetenciaController extends Controller
{
    use HandlesCompetenciaCrudReadActions;
    use HandlesCompetenciaCrudWriteActions;
    use HandlesCompetenciaEstadoActions;
    use HandlesCompetenciaResultadosActions;
    use HandlesCompetenciaUpdateActions;

    protected CompetenciaService $competenciaService;

    protected CompetenciaRepository $competenciaRepo;

    protected ResultadosAprendizajeRepository $resultadosRepo;

    public function __construct(
        CompetenciaService $competenciaService,
        CompetenciaRepository $competenciaRepo,
        ResultadosAprendizajeRepository $resultadosRepo
    ) {
        $this->middleware('auth');
        $this->competenciaService = $competenciaService;
        $this->competenciaRepo = $competenciaRepo;
        $this->resultadosRepo = $resultadosRepo;

        $this->middleware('can:VER COMPETENCIA')->only(['index', 'show']);
        $this->middleware('can:CREAR COMPETENCIA')->only(['create', 'store']);
        $this->middleware('can:EDITAR COMPETENCIA')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR COMPETENCIA')->only('destroy');
        $this->middleware('can:CAMBIAR ESTADO COMPETENCIA')->only('cambiarEstado');
        $this->middleware('can:GESTIONAR RESULTADOS COMPETENCIA')->only(['gestionarResultados', 'asociarResultado', 'asociarResultados', 'desasociarResultado']);
    }
}
