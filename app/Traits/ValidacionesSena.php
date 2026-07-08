<?php

namespace App\Traits;

use App\Traits\ValidacionesSena\ValidacionesSenaAmbienteDisponibilidad;
use App\Traits\ValidacionesSena\ValidacionesSenaAmbientePertinencia;
use App\Traits\ValidacionesSena\ValidacionesSenaAprendices;
use App\Traits\ValidacionesSena\ValidacionesSenaFechas;
use App\Traits\ValidacionesSena\ValidacionesSenaFichaReglasNegocio;
use App\Traits\ValidacionesSena\ValidacionesSenaFichaUnicidad;
use App\Traits\ValidacionesSena\ValidacionesSenaInstructorAsignacion;
use App\Traits\ValidacionesSena\ValidacionesSenaInstructorDisponibilidad;

trait ValidacionesSena
{
    use ValidacionesSenaAmbienteDisponibilidad;
    use ValidacionesSenaAmbientePertinencia;
    use ValidacionesSenaAprendices;
    use ValidacionesSenaFechas;
    use ValidacionesSenaFichaReglasNegocio;
    use ValidacionesSenaFichaUnicidad;
    use ValidacionesSenaInstructorAsignacion;
    use ValidacionesSenaInstructorDisponibilidad;
}
