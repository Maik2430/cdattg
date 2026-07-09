<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Concerns\Auth\HandlesRegisterCrudReadActions;
use App\Http\Controllers\Concerns\Auth\HandlesRegisterCrudWriteActions;
use App\Http\Controllers\Controller;
use App\Repositories\TemaRepository;

class RegisterController extends Controller
{
    use HandlesRegisterCrudReadActions;
    use HandlesRegisterCrudWriteActions;

    protected TemaRepository $temaRepository;

    public function __construct(TemaRepository $temaRepository)
    {
        $this->middleware('guest');
        $this->temaRepository = $temaRepository;
    }
}
