<?php

namespace App\Services\Complementarios;

use App\Repositories\Complementarios\AspiranteComplementarioRepository;
use App\Repositories\Complementarios\ComplementarioOfertadoRepository;
use App\Repositories\PersonaRepository;
use App\Repositories\TemaRepository;
use App\Services\Concerns\Complementarios\InscripcionComplementario\HandlesInscripcionComplementarioFormActions;
use App\Services\Concerns\Complementarios\InscripcionComplementario\HandlesInscripcionComplementarioPersonaHelpers;
use App\Services\Concerns\Complementarios\InscripcionComplementario\HandlesInscripcionComplementarioProcessActions;
use App\Services\Concerns\Complementarios\InscripcionComplementario\HandlesInscripcionComplementarioTemaHelpers;
use App\Services\UserService;

class InscripcionComplementarioService
{
    use HandlesInscripcionComplementarioFormActions;
    use HandlesInscripcionComplementarioPersonaHelpers;
    use HandlesInscripcionComplementarioProcessActions;
    use HandlesInscripcionComplementarioTemaHelpers;

    protected PersonaRepository $personaRepository;

    protected AspiranteComplementarioRepository $aspiranteRepository;

    protected ComplementarioOfertadoRepository $programaRepository;

    protected TemaRepository $temaRepository;

    protected ComplementarioService $complementarioService;

    protected AspiranteDocumentoService $documentoService;

    protected UserService $userService;

    public function __construct(
        PersonaRepository $personaRepository,
        AspiranteComplementarioRepository $aspiranteRepository,
        ComplementarioOfertadoRepository $programaRepository,
        TemaRepository $temaRepository,
        ComplementarioService $complementarioService,
        AspiranteDocumentoService $documentoService,
        UserService $userService
    ) {
        $this->personaRepository = $personaRepository;
        $this->aspiranteRepository = $aspiranteRepository;
        $this->programaRepository = $programaRepository;
        $this->temaRepository = $temaRepository;
        $this->complementarioService = $complementarioService;
        $this->documentoService = $documentoService;
        $this->userService = $userService;
    }
}
