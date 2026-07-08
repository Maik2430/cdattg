<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistroAsistencia\HandlesRegistroAsistenciaReadActions;
use App\Http\Controllers\Concerns\RegistroAsistencia\HandlesRegistroAsistenciaWriteActions;

/**
 * Controlador para el registro de asistencias por jornada
 * Responsabilidad: Registrar entradas/salidas y organizar por jornada
 */
class RegistroAsistenciaController extends Controller
{
    use HandlesRegistroAsistenciaReadActions;
    use HandlesRegistroAsistenciaWriteActions;
}
