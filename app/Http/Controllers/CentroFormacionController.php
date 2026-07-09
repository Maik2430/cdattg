<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\CentroFormacion\HandlesCentroFormacionCrudReadActions;
use App\Http\Controllers\Concerns\CentroFormacion\HandlesCentroFormacionCrudWriteActions;
use App\Http\Controllers\Concerns\CentroFormacion\HandlesCentroFormacionEstadoActions;

class CentroFormacionController extends Controller
{
    use HandlesCentroFormacionCrudReadActions;
    use HandlesCentroFormacionCrudWriteActions;
    use HandlesCentroFormacionEstadoActions;

    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('can:VER CENTRO DE FORMACION')->only(['index', 'show']);
        $this->middleware('can:CREAR CENTRO DE FORMACION')->only(['create', 'store']);
        $this->middleware('can:EDITAR CENTRO DE FORMACION')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR CENTRO DE FORMACION')->only('destroy');
    }
}
