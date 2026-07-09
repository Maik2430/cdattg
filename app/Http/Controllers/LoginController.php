<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Login\HandlesLoginApiActions;
use App\Http\Controllers\Concerns\Login\HandlesLoginCrudReadActions;
use App\Http\Controllers\Concerns\Login\HandlesLoginCrudWriteActions;
use App\Http\Controllers\Concerns\Login\HandlesLoginVerificationActions;

class LoginController extends Controller
{
    use HandlesLoginApiActions;
    use HandlesLoginCrudReadActions;
    use HandlesLoginCrudWriteActions;
    use HandlesLoginVerificationActions;
}
