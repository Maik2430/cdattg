<?php

namespace App\Services\Complementarios;

use App\Repositories\Complementarios\AspiranteComplementarioRepository;
use App\Repositories\Complementarios\ComplementarioOfertadoRepository;
use App\Repositories\TemaRepository;
use App\Services\Concerns\Complementarios\Complementario\HandlesComplementarioAspiranteActions;
use App\Services\Concerns\Complementarios\Complementario\HandlesComplementarioDisplayHelpers;
use App\Services\Concerns\Complementarios\Complementario\HandlesComplementarioFormDataHelpers;
use App\Services\Concerns\Complementarios\Complementario\HandlesComplementarioProgramaActions;

class ComplementarioService
{
    use HandlesComplementarioAspiranteActions;
    use HandlesComplementarioDisplayHelpers;
    use HandlesComplementarioFormDataHelpers;
    use HandlesComplementarioProgramaActions;

    public function __construct(
        private readonly TemaRepository $temaRepository,
        private readonly ComplementarioOfertadoRepository $programaRepository,
        private readonly AspiranteComplementarioRepository $aspiranteRepository
    ) {}
}
