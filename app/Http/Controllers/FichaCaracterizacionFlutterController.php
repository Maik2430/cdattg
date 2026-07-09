<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FichaCaracterizacionFlutter\HandlesFichaCaracterizacionFlutterApiActions;
use App\Http\Controllers\Concerns\FichaCaracterizacionFlutter\HandlesFichaCaracterizacionFlutterAprendizActions;

class FichaCaracterizacionFlutterController extends Controller
{
    use HandlesFichaCaracterizacionFlutterApiActions;
    use HandlesFichaCaracterizacionFlutterAprendizActions;
}
