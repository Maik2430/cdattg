<?php

namespace App\Http\Controllers\Concerns\Permiso;

use App\Models\User;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

trait HandlesPermisoCrudReadActions
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('permisos.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $user = User::find($id);
        $permisos = Permission::all();
        $roles = Role::all();

        return view('permisos.show', compact('user', 'permisos', 'roles'));
    }
}
