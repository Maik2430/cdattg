<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AsignacionInstructor\HandlesAsignacionInstructorApiActions;
use App\Http\Controllers\Concerns\AsignacionInstructor\HandlesAsignacionInstructorCrudReadActions;
use App\Http\Controllers\Concerns\AsignacionInstructor\HandlesAsignacionInstructorCrudWriteActions;

class AsignacionInstructorController extends Controller
{
    use HandlesAsignacionInstructorApiActions;
    use HandlesAsignacionInstructorCrudReadActions;
    use HandlesAsignacionInstructorCrudWriteActions;

    public function __construct()
    {
        $this->middleware('auth');
    }
}
