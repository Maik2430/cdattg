<?php

declare(strict_types=1);

namespace App\Services\Complementarios;

use App\Repositories\Complementarios\AspiranteComplementarioRepository;
use App\Repositories\Complementarios\ComplementarioOfertadoRepository;
use App\Repositories\PersonaRepository;
use App\Services\Concerns\Complementarios\EstadisticaComplementario\HandlesEstadisticaComplementarioExportActions;
use App\Services\Concerns\Complementarios\EstadisticaComplementario\HandlesEstadisticaComplementarioQueryActions;

class EstadisticaComplementarioService
{
    use HandlesEstadisticaComplementarioExportActions;
    use HandlesEstadisticaComplementarioQueryActions;

    public function __construct(
        private readonly AspiranteComplementarioRepository $aspiranteRepository,
        private readonly ComplementarioOfertadoRepository $programaRepository,
        private readonly PersonaRepository $personaRepository
    ) {}
}
