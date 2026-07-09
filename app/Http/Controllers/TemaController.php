<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Tema\HandlesTemaCrudReadActions;
use App\Http\Controllers\Concerns\Tema\HandlesTemaCrudWriteActions;
use App\Http\Controllers\Concerns\Tema\HandlesTemaEstadoActions;
use App\Http\Controllers\Concerns\Tema\HandlesTemaParametroActions;
use App\Services\TemaService;

class TemaController extends Controller
{
    use HandlesTemaCrudReadActions;
    use HandlesTemaCrudWriteActions;
    use HandlesTemaEstadoActions;
    use HandlesTemaParametroActions;

    protected TemaService $temaService;

    /**
     * Display a listing of the resource.
     */
    public function __construct(TemaService $temaService)
    {
        $this->middleware('auth');
        $this->temaService = $temaService;

        $this->middleware('can:VER TEMA')->only(['index', 'show']);
        $this->middleware('can:CREAR TEMA')->only(['create', 'store']);
        $this->middleware('can:EDITAR TEMA')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR TEMA')->only('destroy');
    }
}
