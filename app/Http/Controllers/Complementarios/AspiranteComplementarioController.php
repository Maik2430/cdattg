<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Complementarios\Concerns\HandlesAspiranteComplementarioExportActions;
use App\Http\Controllers\Complementarios\Concerns\HandlesAspiranteComplementarioFormActions;
use App\Http\Controllers\Complementarios\Concerns\HandlesAspiranteComplementarioReadActions;
use App\Http\Controllers\Complementarios\Concerns\HandlesAspiranteComplementarioStoreNewActions;
use App\Http\Controllers\Complementarios\Concerns\HandlesAspiranteComplementarioWriteActions;
use App\Http\Controllers\Controller;
use App\Repositories\Complementarios\AspiranteComplementarioRepository;
use App\Repositories\Complementarios\ComplementarioOfertadoRepository;
use App\Repositories\TemaRepository;
use App\Services\Complementarios\AspiranteDocumentoService;
use App\Services\Complementarios\AspiranteExportService;
use App\Services\Complementarios\AspiranteManagementService;
use App\Services\Complementarios\ComplementarioService;
use App\Services\PersonaService;

class AspiranteComplementarioController extends Controller
{
    use HandlesAspiranteComplementarioExportActions;
    use HandlesAspiranteComplementarioFormActions;
    use HandlesAspiranteComplementarioReadActions;
    use HandlesAspiranteComplementarioStoreNewActions;
    use HandlesAspiranteComplementarioWriteActions;

    private const ERROR_MENSAJE_SERVIDOR = 'Error interno del servidor. Por favor intente nuevamente.';

    public function __construct(
        private readonly AspiranteManagementService $aspiranteManagementService,
        private readonly AspiranteExportService $exportService,
        private readonly AspiranteDocumentoService $documentoService,
        private readonly PersonaService $personaService,
        private readonly AspiranteComplementarioRepository $aspiranteRepository,
        private readonly ComplementarioOfertadoRepository $programaRepository,
        private readonly ComplementarioService $complementarioService,
        private readonly TemaRepository $temaRepository
    ) {}
}
