<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RedConocimiento\HandlesRedConocimientoCrudReadActions;
use App\Http\Controllers\Concerns\RedConocimiento\HandlesRedConocimientoCrudWriteActions;
use App\Http\Controllers\Concerns\RedConocimiento\HandlesRedConocimientoEstadoActions;
use App\Services\RedConocimientoService;

class RedConocimientoController extends Controller
{
    use HandlesRedConocimientoCrudReadActions;
    use HandlesRedConocimientoCrudWriteActions;
    use HandlesRedConocimientoEstadoActions;

    protected RedConocimientoService $redService;

    /**
     * Constructor: aplica middleware de autenticación y permisos.
     */
    public function __construct(RedConocimientoService $redService)
    {
        $this->middleware('auth');
        $this->redService = $redService;

        $this->middleware('can:VER RED CONOCIMIENTO')->only(['index', 'show']);
        $this->middleware('can:CREAR RED CONOCIMIENTO')->only(['create', 'store']);
        $this->middleware('can:EDITAR RED CONOCIMIENTO')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR RED CONOCIMIENTO')->only('destroy');
    }
}
