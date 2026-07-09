<?php

namespace App\Http\Controllers\Concerns\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

trait HandlesRegisterCrudWriteActions
{
    use HandlesRegisterPersonaCreationHelpers;
    use HandlesRegisterRoleHelpers;

    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($this->personaExists($validated['numero_documento'], $validated['email'])) {
            return back()
                ->withInput()
                ->with('error', 'Ya existe una persona registrada con este número de documento o correo electrónico.');
        }

        try {
            [$persona, $user] = DB::transaction(function () use ($validated) {
                return $this->createPersonaYUsuario($validated);
            });
        } catch (Throwable $e) {
            Log::error('Error al registrar usuario', [
                'error' => $e->getMessage(),
                'documento' => $validated['numero_documento'] ?? null,
            ]);

            return back()->withInput()->with('error', 'No fue posible completar el registro. Intente nuevamente.');
        }

        $this->actualizarRolesSegunInscripcion($persona, $user);

        // Enviar email de verificación
        $user->sendEmailVerificationNotification();

        Auth::login($user);

        return redirect()
            ->route('verification.notice')
            ->with('success', '¡Registro exitoso! Por favor, verifica tu correo electrónico antes de continuar.');
    }
}
