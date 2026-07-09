<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Concerns\ValidarFichaCaracterizacion\HandlesValidarFichaCaracterizacionDataHelpers;
use App\Http\Middleware\Concerns\ValidarFichaCaracterizacion\HandlesValidarFichaCaracterizacionRequestActions;
use App\Services\FichaCaracterizacionValidationService;

class ValidarFichaCaracterizacion
{
    use HandlesValidarFichaCaracterizacionDataHelpers;
    use HandlesValidarFichaCaracterizacionRequestActions;

    public function __construct()
    {
        $this->validationService = new FichaCaracterizacionValidationService;
    }
}
