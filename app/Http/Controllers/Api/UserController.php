<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Obtener los permisos del usuario autenticado
     */
    public function permissions(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $user = Auth::user();
        
        // Obtener todos los permisos del usuario a través de sus roles
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray(),
            ],
            'permissions' => $permissions,
            'total_permissions' => count($permissions)
        ]);
    }
}
