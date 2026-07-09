<?php

namespace App\Http\Controllers\Concerns\Login;

use App\Models\User;

trait HandlesLoginValidationHelpers
{
    /**
     * Valida si el usuario puede iniciar sesión antes de intentar autenticar
     */
    private function validarUsuarioAntesLogin(?User $user): bool
    {
        if (! $user) {
            return true; // Permitir intento de autenticación
        }

        return $user->hasVerifiedEmail() && $user->status == 1;
    }

    /**
     * Obtiene la respuesta de validación cuando el usuario no puede iniciar sesión
     */
    private function getRespuestaValidacionUsuario(User $user)
    {
        $response = null;

        if (! $user->hasVerifiedEmail()) {
            // Enviar correo de verificación automáticamente
            try {
                \Illuminate\Support\Facades\Log::info('Enviando correo de verificación automático al intentar login', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);

                $user->sendEmailVerificationNotification();

                \Illuminate\Support\Facades\Log::info('Correo de verificación enviado automáticamente', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);

                $response = back()
                    ->withInput()
                    ->with(
                        'error',
                        'Debes verificar tu correo electrónico antes de iniciar sesión. '
                        .'Se ha enviado un nuevo enlace de verificación a tu correo. '
                        .'Revisa tu bandeja de entrada y spam.'
                    );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error al enviar correo de verificación automático', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);

                $response = back()
                    ->withInput()
                    ->with(
                        'error',
                        'Debes verificar tu correo electrónico antes de iniciar sesión. '
                        .'No se pudo enviar el correo automáticamente. '
                        .'Solicita un nuevo enlace de verificación usando el formulario a continuación.'
                    );
            }
        } elseif ($user->status == 0) {
            $response = back()
                ->withInput()
                ->withErrors(['email' => 'Tu cuenta se encuentra inactiva. Contacta al administrador.']);
        } else {
            $response = back()
                ->withInput()
                ->withErrors(['error' => 'No se puede iniciar sesión']);
        }

        return $response;
    }
}
