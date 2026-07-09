<?php

namespace App\Observers;

use App\Observers\Concerns\AspiranteComplementario\HandlesAspiranteComplementarioCreatedActions;
use App\Observers\Concerns\AspiranteComplementario\HandlesAspiranteComplementarioLifecycleActions;
use App\Observers\Concerns\AspiranteComplementario\HandlesAspiranteComplementarioReplicationHelpers;
use App\Repositories\Complementarios\ComplementarioOfertadoRepository;

class AspiranteComplementarioObserver
{
    use HandlesAspiranteComplementarioCreatedActions;
    use HandlesAspiranteComplementarioLifecycleActions;
    use HandlesAspiranteComplementarioReplicationHelpers;

    public function __construct(
        private readonly ComplementarioOfertadoRepository $complementarioRepository
    ) {}
}
