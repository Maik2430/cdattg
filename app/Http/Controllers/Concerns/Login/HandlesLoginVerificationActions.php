<?php

namespace App\Http\Controllers\Concerns\Login;

use App\Models\User;
use Illuminate\Http\Request;

trait HandlesLoginVerificationActions
{
    /**
     * Reenvía el correo de verificación sin autenticación (desde el login)
     */
    public function reenviarCorreoVerificacion(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No existe una cuenta registrada con este correo electrónico.',
        ]);

        $user = User::where('email', $validated['email'])->first();
        $response = null;

        if (! $user) {
            $response = back()
                ->withInput()
                ->withErrors(['email' => 'No existe una cuenta registrada con este correo electrónico.']);
        } elseif ($user->hasVerifiedEmail()) {
            $response = back()
                ->withInput()
                ->with('info', 'Tu correo electrónico ya está verificado. Puedes iniciar sesión.');
        } else {
            try {
                \Illuminate\Support\Facades\Log::info('Intentando enviar correo de verificación', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);

                $user->sendEmailVerificationNotification();

                \Illuminate\Support\Facades\Log::info('Correo de verificación enviado exitosamente', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);

                $response = back()
                    ->withInput()
                    ->with(
                        'success',
                        'Se ha enviado un nuevo enlace de verificación a tu correo electrónico. '
                        .'Por favor, revisa tu bandeja de entrada y spam.'
                    );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error al enviar correo de verificación', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'class' => get_class($e),
                ]);

                $errorMessage = 'No se pudo enviar el correo de verificación.';
                if (str_contains($e->getMessage(), 'Connection') || str_contains($e->getMessage(), 'SMTP')) {
                    $errorMessage .= ' Verifica la configuración SMTP.';
                }
                $errorMessage .= ' Error: '.$e->getMessage();

                $response = back()
                    ->withInput()
                    ->withErrors(['error' => $errorMessage]);
            }
        }

        return $response;
    }
}
