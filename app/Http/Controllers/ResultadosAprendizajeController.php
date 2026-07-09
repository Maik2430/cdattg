<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResultadosAprendizaje\HandlesResultadosAprendizajeCompetenciasActions;
use App\Http\Controllers\Concerns\ResultadosAprendizaje\HandlesResultadosAprendizajeCrudReadActions;
use App\Http\Controllers\Concerns\ResultadosAprendizaje\HandlesResultadosAprendizajeCrudWriteActions;
use App\Repositories\CompetenciaRepository;
use App\Repositories\ResultadosAprendizajeRepository;

class ResultadosAprendizajeController extends Controller
{
    use HandlesResultadosAprendizajeCompetenciasActions;
    use HandlesResultadosAprendizajeCrudReadActions;
    use HandlesResultadosAprendizajeCrudWriteActions;

    protected ResultadosAprendizajeRepository $resultadoRepo;

    protected CompetenciaRepository $competenciaRepo;

    public function __construct(
        ResultadosAprendizajeRepository $resultadoRepo,
        CompetenciaRepository $competenciaRepo
    ) {
        $this->middleware('auth');
        $this->resultadoRepo = $resultadoRepo;
        $this->competenciaRepo = $competenciaRepo;

        $this->middleware('can:VER RESULTADO APRENDIZAJE')->only(['index', 'show']);
        $this->middleware('can:CREAR RESULTADO APRENDIZAJE')->only(['create', 'store']);
        $this->middleware('can:EDITAR RESULTADO APRENDIZAJE')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR RESULTADO APRENDIZAJE')->only('destroy');
    }
}
