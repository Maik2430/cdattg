<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Aprendiz\HandlesAprendizApiActions;
use App\Http\Controllers\Concerns\Aprendiz\HandlesAprendizCrudReadActions;
use App\Http\Controllers\Concerns\Aprendiz\HandlesAprendizCrudWriteActions;
use App\Http\Controllers\Concerns\Aprendiz\HandlesAprendizDatatableActions;
use App\Http\Controllers\Concerns\Aprendiz\HandlesAprendizEstadoActions;
use App\Services\AprendizService;

class AprendizController extends Controller
{
    use HandlesAprendizApiActions;
    use HandlesAprendizCrudReadActions;
    use HandlesAprendizCrudWriteActions;
    use HandlesAprendizDatatableActions;
    use HandlesAprendizEstadoActions;

    protected AprendizService $aprendizService;

    public function __construct(AprendizService $aprendizService)
    {
        $this->middleware('auth');
        $this->aprendizService = $aprendizService;

        $this->middleware('can:VER APRENDIZ')->only(['index', 'show']);
        $this->middleware('can:CREAR APRENDIZ')->only(['create', 'store']);
        $this->middleware('can:EDITAR APRENDIZ')->only(['edit', 'update', 'cambiarEstado']);
        $this->middleware('can:ELIMINAR APRENDIZ')->only('destroy');
    }
}
