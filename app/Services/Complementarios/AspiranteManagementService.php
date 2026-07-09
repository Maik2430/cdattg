<?php

namespace App\Services\Complementarios;

use App\Repositories\Complementarios\AspiranteComplementarioRepository;
use App\Repositories\Complementarios\ComplementarioOfertadoRepository;
use App\Repositories\PersonaRepository;
use App\Services\Concerns\Complementarios\AspiranteManagement\HandlesAspiranteManagementConstants;
use App\Services\Concerns\Complementarios\AspiranteManagement\HandlesAspiranteManagementDocumentStorageActions;
use App\Services\Concerns\Complementarios\AspiranteManagement\HandlesAspiranteManagementDocumentValidationActions;
use App\Services\Concerns\Complementarios\AspiranteManagement\HandlesAspiranteManagementReadActions;
use App\Services\Concerns\Complementarios\AspiranteManagement\HandlesAspiranteManagementResponseHelpers;
use App\Services\Concerns\Complementarios\AspiranteManagement\HandlesAspiranteManagementValidationHelpers;
use App\Services\Concerns\Complementarios\AspiranteManagement\HandlesAspiranteManagementWriteActions;

class AspiranteManagementService
{
    use HandlesAspiranteManagementConstants;
    use HandlesAspiranteManagementDocumentStorageActions;
    use HandlesAspiranteManagementDocumentValidationActions;
    use HandlesAspiranteManagementReadActions;
    use HandlesAspiranteManagementResponseHelpers;
    use HandlesAspiranteManagementValidationHelpers;
    use HandlesAspiranteManagementWriteActions;

    protected AspiranteComplementarioRepository $aspiranteRepository;

    protected ComplementarioOfertadoRepository $programaRepository;

    protected PersonaRepository $personaRepository;

    protected AspiranteDocumentoService $documentoService;

    public function __construct(
        AspiranteComplementarioRepository $aspiranteRepository,
        ComplementarioOfertadoRepository $programaRepository,
        PersonaRepository $personaRepository,
        AspiranteDocumentoService $documentoService
    ) {
        $this->aspiranteRepository = $aspiranteRepository;
        $this->programaRepository = $programaRepository;
        $this->personaRepository = $personaRepository;
        $this->documentoService = $documentoService;
    }
}
