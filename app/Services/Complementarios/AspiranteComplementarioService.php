<?php

namespace App\Services\Complementarios;

use App\Repositories\Complementarios\AspiranteComplementarioRepository;
use App\Repositories\PersonaRepository;
use App\Services\Concerns\Complementarios\AspiranteComplementario\HandlesAspiranteComplementarioDescargaActions;
use App\Services\Concerns\Complementarios\AspiranteComplementario\HandlesAspiranteComplementarioReadActions;
use App\Services\Concerns\Complementarios\AspiranteComplementario\HandlesAspiranteComplementarioValidacionActions;

class AspiranteComplementarioService
{
    use HandlesAspiranteComplementarioDescargaActions;
    use HandlesAspiranteComplementarioReadActions;
    use HandlesAspiranteComplementarioValidacionActions;

    protected AspiranteDocumentoService $documentoService;

    protected AspiranteComplementarioRepository $aspiranteRepository;

    protected PersonaRepository $personaRepository;

    public function __construct(
        AspiranteDocumentoService $documentoService,
        AspiranteComplementarioRepository $aspiranteRepository,
        PersonaRepository $personaRepository
    ) {
        $this->documentoService = $documentoService;
        $this->aspiranteRepository = $aspiranteRepository;
        $this->personaRepository = $personaRepository;
    }
}
