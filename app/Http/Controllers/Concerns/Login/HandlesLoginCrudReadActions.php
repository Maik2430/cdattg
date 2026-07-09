<?php

namespace App\Http\Controllers\Concerns\Login;

use Illuminate\Support\Facades\Auth;

trait HandlesLoginCrudReadActions
{
    public function index()
    {
        return view('adminlte::auth.login');
    }

    public function verificarLogin()
    {
        return Auth::check() ? redirect('/home') : redirect('/login');
    }
}
