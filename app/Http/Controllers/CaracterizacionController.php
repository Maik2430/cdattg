<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Caracterizacion\HandlesCaracterizacionApiActions;
use App\Http\Controllers\Concerns\Caracterizacion\HandlesCaracterizacionCrudReadActions;
use App\Http\Controllers\Concerns\Caracterizacion\HandlesCaracterizacionCrudWriteActions;

class CaracterizacionController extends Controller
{
    use HandlesCaracterizacionApiActions;
    use HandlesCaracterizacionCrudReadActions;
    use HandlesCaracterizacionCrudWriteActions;
}
