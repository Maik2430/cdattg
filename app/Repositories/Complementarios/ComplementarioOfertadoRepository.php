<?php

namespace App\Repositories\Complementarios;

use App\Repositories\Concerns\Complementarios\ComplementarioOfertado\HandlesComplementarioOfertadoEstadoHelpers;
use App\Repositories\Concerns\Complementarios\ComplementarioOfertado\HandlesComplementarioOfertadoQueryActions;
use App\Repositories\Concerns\Complementarios\ComplementarioOfertado\HandlesComplementarioOfertadoStatisticsActions;

class ComplementarioOfertadoRepository
{
    use HandlesComplementarioOfertadoEstadoHelpers;
    use HandlesComplementarioOfertadoQueryActions;
    use HandlesComplementarioOfertadoStatisticsActions;
}
