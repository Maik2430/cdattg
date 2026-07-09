<?php

namespace App\Http\Controllers\Concerns\Permiso;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesPermisoRoleApiActions
{
    /**
     * Assign a specific role to a user (API endpoint for real-time updates)
     */
    public function asignarRol(Request $request, string $userId, string $roleName): JsonResponse
    {
        try {
            // Validate that user exists
            $user = User::findOrFail($userId);

            // Prevent self-modification
            if ($userId == auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes modificar tus propios roles.',
                ], 403);
            }

            // Check if user already has the role
            if ($user->hasRole($roleName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario ya tiene este rol.',
                ], 400);
            }

            // Assign role
            $user->assignRole($roleName);

            return response()->json([
                'success' => true,
                'message' => 'Rol asignado correctamente.',
                'data' => [
                    'user_id' => $userId,
                    'role' => $roleName,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Error al asignar rol: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al asignar el rol.',
            ], 500);
        }
    }

    /**
     * Remove a specific role from a user (API endpoint for real-time updates)
     */
    public function removerRol(Request $request, string $userId, string $roleName): JsonResponse
    {
        try {
            // Validate that user exists
            $user = User::findOrFail($userId);

            // Prevent self-modification
            if ($userId == auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes modificar tus propios roles.',
                ], 403);
            }

            // Check if user has the role
            if (! $user->hasRole($roleName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no tiene este rol.',
                ], 400);
            }

            // Remove role
            $user->removeRole($roleName);

            return response()->json([
                'success' => true,
                'message' => 'Rol removido correctamente.',
                'data' => [
                    'user_id' => $userId,
                    'role' => $roleName,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Error al remover rol: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al remover el rol.',
            ], 500);
        }
    }
}
