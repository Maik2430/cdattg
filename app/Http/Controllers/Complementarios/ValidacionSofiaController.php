<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Concerns\Complementarios\ValidacionSofia\HandlesValidacionSofiaActions;
use App\Http\Controllers\Concerns\Complementarios\ValidacionSofia\HandlesValidacionSofiaProgressHelpers;
use App\Http\Controllers\Concerns\Complementarios\ValidacionSofia\HandlesValidacionSofiaValidationHelpers;
use App\Http\Controllers\Controller;

class ValidacionSofiaController extends Controller
{
    use HandlesValidacionSofiaActions;
    use HandlesValidacionSofiaProgressHelpers;
    use HandlesValidacionSofiaValidationHelpers;
}
