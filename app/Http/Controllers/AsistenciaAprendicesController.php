<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AsistenciaAprendices\HandlesAsistenciaAprendicesReadActions;
use App\Http\Controllers\Concerns\AsistenciaAprendices\HandlesAsistenciaAprendicesWriteActions;
use App\Services\AsistenciaService;
use App\Services\JornadaValidationService;

class AsistenciaAprendicesController extends Controller
{
    use HandlesAsistenciaAprendicesReadActions;
    use HandlesAsistenciaAprendicesWriteActions;

    protected AsistenciaService $asistenciaService;

    protected JornadaValidationService $jornadaValidation;

    public function __construct(
        AsistenciaService $asistenciaService,
        JornadaValidationService $jornadaValidation
    ) {
        $this->asistenciaService = $asistenciaService;
        $this->jornadaValidation = $jornadaValidation;
    }
}
