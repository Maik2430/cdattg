<?php

namespace App\Http\Controllers\Concerns\Login;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait HandlesLoginSuccessHelpers
{
    use HandlesLoginFormDataHelpers;

    /**
     * Procesa el login exitoso verificando estado y redirigiendo
     */
    private function procesarLoginExitoso(Request $request, User $user)
    {
        if ($user->status == 0) {
            Auth::logout();

            return back()
                ->withInput()
                ->withErrors(['error' => 'La cuenta se encuentra inactiva']);
        }

        if (! $user->hasVerifiedEmail()) {
            Auth::logout();

            // Enviar correo de verificación automáticamente
            try {
                \Illuminate\Support\Facades\Log::info(
                    'Enviando correo de verificación automático después de login exitoso',
                    [
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ]
                );

                $user->sendEmailVerificationNotification();

                \Illuminate\Support\Facades\Log::info(
                    'Correo de verificación enviado automáticamente después de login',
                    [
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ]
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error(
                    'Error al enviar correo de verificación automático después de login',
                    [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'error' => $e->getMessage(),
                    ]
                );
            }

            return redirect()
                ->route('verification.notice')
                ->with(
                    'warning',
                    'Por favor, verifica tu correo electrónico antes de iniciar sesión. '
                    .'Se ha enviado un enlace de verificación a tu correo. Revisa tu bandeja de entrada y spam.'
                );
        }

        return $this->redirigirDespuesLogin($request, $user);
    }

    /**
     * Redirige al usuario después de un login exitoso
     */
    private function redirigirDespuesLogin(Request $request, User $user)
    {
        $redirect = $request->input('redirect') ?: $request->query('redirect');

        if ($redirect) {
            return redirect('/programas-complementarios/'.$redirect.'/inscripcion')
                ->with('user_data', $this->getUserDataForForm($user))
                ->with(
                    'success',
                    '¡Sesión Iniciada! Complete su información para finalizar la inscripción.'
                );
        }

        return redirect('/home')->with('success', '¡Sesión Iniciada!');
    }
}
