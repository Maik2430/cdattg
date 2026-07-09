<?php

namespace App\Repositories\Complementarios;

use App\Repositories\Concerns\Complementarios\AspiranteComplementario\HandlesAspiranteComplementarioQueryActions;
use App\Repositories\Concerns\Complementarios\AspiranteComplementario\HandlesAspiranteComplementarioStatisticsActions;
use App\Repositories\Concerns\Complementarios\AspiranteComplementario\HandlesAspiranteComplementarioWriteActions;

class AspiranteComplementarioRepository
{
    use HandlesAspiranteComplementarioQueryActions;
    use HandlesAspiranteComplementarioStatisticsActions;
    use HandlesAspiranteComplementarioWriteActions;
}
