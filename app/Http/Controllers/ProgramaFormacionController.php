<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ProgramaFormacion\HandlesProgramaFormacionApiActions;
use App\Http\Controllers\Concerns\ProgramaFormacion\HandlesProgramaFormacionCompetenciasActions;
use App\Http\Controllers\Concerns\ProgramaFormacion\HandlesProgramaFormacionCrudReadActions;
use App\Http\Controllers\Concerns\ProgramaFormacion\HandlesProgramaFormacionCrudWriteActions;
use App\Http\Controllers\Concerns\ProgramaFormacion\HandlesProgramaFormacionEstadoActions;
use App\Services\ProgramaFormacionService;

class ProgramaFormacionController extends Controller
{
    use HandlesProgramaFormacionApiActions;
    use HandlesProgramaFormacionCompetenciasActions;
    use HandlesProgramaFormacionCrudReadActions;
    use HandlesProgramaFormacionCrudWriteActions;
    use HandlesProgramaFormacionEstadoActions;

    protected ProgramaFormacionService $programaService;

    public function __construct(ProgramaFormacionService $programaService)
    {
        $this->middleware('auth');
        $this->programaService = $programaService;

        $this->middleware('permission:VER PROGRAMAS DE FORMACION')->only('index');
        $this->middleware('permission:VER PROGRAMA DE FORMACION')->only('show');
        $this->middleware('permission:CREAR PROGRAMA DE FORMACION')->only('create', 'store');
        $this->middleware('permission:EDITAR PROGRAMA DE FORMACION')->only('edit', 'update');
        $this->middleware('permission:ELIMINAR PROGRAMA DE FORMACION')->only('destroy');
        $this->middleware('permission:VER PROGRAMAS DE FORMACION')->only('search');
        $this->middleware('permission:CAMBIAR ESTADO PROGRAMA DE FORMACION')->only('cambiarEstado');
    }
}
