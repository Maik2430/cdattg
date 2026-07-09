<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GuiaAprendizaje\HandlesGuiaAprendizajeApiActions;
use App\Http\Controllers\Concerns\GuiaAprendizaje\HandlesGuiaAprendizajeCrudReadActions;
use App\Http\Controllers\Concerns\GuiaAprendizaje\HandlesGuiaAprendizajeCrudWriteActions;
use App\Http\Controllers\Concerns\GuiaAprendizaje\HandlesGuiaAprendizajeResultadosActions;
use App\Repositories\GuiasAprendizajeRepository;
use App\Services\GuiaAprendizajeService;

class GuiaAprendizajeController extends Controller
{
    use HandlesGuiaAprendizajeApiActions;
    use HandlesGuiaAprendizajeCrudReadActions;
    use HandlesGuiaAprendizajeCrudWriteActions;
    use HandlesGuiaAprendizajeResultadosActions;

    protected GuiaAprendizajeService $guiaService;

    protected GuiasAprendizajeRepository $guiaRepo;

    public function __construct(
        GuiaAprendizajeService $guiaService,
        GuiasAprendizajeRepository $guiaRepo
    ) {
        $this->middleware('auth');
        $this->guiaService = $guiaService;
        $this->guiaRepo = $guiaRepo;

        $this->middleware('can:VER GUIA APRENDIZAJE')->only(['index', 'show']);
        $this->middleware('can:CREAR GUIA APRENDIZAJE')->only(['create', 'store']);
        $this->middleware('can:EDITAR GUIA APRENDIZAJE')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR GUIA APRENDIZAJE')->only('destroy');
    }
}
