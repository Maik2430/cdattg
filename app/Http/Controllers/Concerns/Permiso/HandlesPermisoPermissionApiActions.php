<?php

namespace App\Http\Controllers\Concerns\Permiso;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesPermisoPermissionApiActions
{
    /**
     * Assign a specific permission to a user (API endpoint for real-time updates)
     */
    public function asignarPermiso(Request $request, string $userId, string $permissionName): JsonResponse
    {
        try {
            // Validate that user exists
            $user = User::findOrFail($userId);

            // Prevent self-modification
            if ($userId == auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes modificar tus propios permisos.',
                ], 403);
            }

            // Check if user already has the permission
            if ($user->hasPermissionTo($permissionName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario ya tiene este permiso.',
                ], 400);
            }

            // Assign permission
            $user->givePermissionTo($permissionName);

            return response()->json([
                'success' => true,
                'message' => 'Permiso asignado correctamente.',
                'data' => [
                    'user_id' => $userId,
                    'permission' => $permissionName,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Error al asignar permiso: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al asignar el permiso.',
            ], 500);
        }
    }

    /**
     * Remove a specific permission from a user (API endpoint for real-time updates)
     */
    public function removerPermiso(Request $request, string $userId, string $permissionName): JsonResponse
    {
        try {
            // Validate that user exists
            $user = User::findOrFail($userId);

            // Prevent self-modification
            if ($userId == auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes modificar tus propios permisos.',
                ], 403);
            }

            // Check if user has the permission
            if (! $user->hasPermissionTo($permissionName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no tiene este permiso.',
                ], 400);
            }

            // Remove permission
            $user->revokePermissionTo($permissionName);

            return response()->json([
                'success' => true,
                'message' => 'Permiso removido correctamente.',
                'data' => [
                    'user_id' => $userId,
                    'permission' => $permissionName,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Error al remover permiso: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al remover el permiso.',
            ], 500);
        }
    }
}
