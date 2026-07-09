<?php

namespace App\Services\Concerns\AprendizRole;

use App\Models\Aprendiz;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

trait HandlesAprendizRoleUserHelpers
{
    private function createUserForPersona(Persona $persona, Aprendiz $aprendiz): ?User
    {
        try {
            $email = $persona->email ?? "aprendiz_{$aprendiz->id}@sena.edu.co";

            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                $email = "aprendiz_{$aprendiz->id}_{$persona->id}@sena.edu.co";
            }

            $user = User::create([
                'email' => $email,
                'password' => Hash::make('123456'),
                'status' => 1,
                'persona_id' => $persona->id,
            ]);

            $user->sendEmailVerificationNotification();

            Log::info('Usuario creado por AprendizRoleService', [
                'aprendiz_id' => $aprendiz->id,
                'user_id' => $user->id,
                'persona_id' => $persona->id,
                'email' => $user->email,
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('Error al crear usuario en AprendizRoleService', [
                'aprendiz_id' => $aprendiz->id,
                'persona_id' => $persona->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
