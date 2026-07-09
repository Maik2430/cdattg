<?php

namespace App\Http\Controllers\Concerns\Persona;

use App\Http\Requests\UpdatePersonaRoleRequest;
use App\Models\Persona;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaUserRoleActions
{
    public function updateRole(UpdatePersonaRoleRequest $request, Persona $persona)
    {
        if (! $persona->user) {
            return redirect()
                ->back()
                ->with('error', 'La persona no tiene un usuario asociado.');
        }

        $rolesSeleccionados = collect($request->validated()['roles'] ?? [])
            ->map(static fn (string $role) => trim($role))
            ->filter(static fn (string $role) => $role !== '')
            ->unique()
            ->values();

        try {
            DB::transaction(static function () use ($persona, $rolesSeleccionados) {
                $user = $persona->user;
                $user->syncRoles($rolesSeleccionados->all());
            });

            return redirect()
                ->route('personas.show', $persona->id)
                ->with('success', 'Roles actualizados correctamente.');
        } catch (\Throwable $exception) {
            Log::error('Error al actualizar el rol de la persona', [
                'persona_id' => $persona->id,
                'roles' => $rolesSeleccionados->all(),
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'No se pudo actualizar el rol de la persona.');
        }
    }

    public function resetPassword(Persona $persona): RedirectResponse
    {
        $response = null;

        if (! $persona->user) {
            $response = redirect()->back()->with('error', 'La persona no tiene un usuario asociado.');
        } elseif (empty($persona->numero_documento)) {
            $response = redirect()->back()->with('error', 'La persona no tiene número de documento registrado.');
        } else {
            try {
                DB::transaction(static function () use ($persona): void {
                    $documento = (string) $persona->numero_documento;
                    $persona->user->forceFill([
                        'password' => Hash::make($documento),
                    ])->save();
                });

                $response = redirect()->back()->with('success', 'Contraseña restablecida correctamente.');
            } catch (\Throwable $exception) {
                Log::error('Error al restablecer contraseña de persona', [
                    'persona_id' => $persona->id,
                    'error' => $exception->getMessage(),
                ]);

                $response = redirect()->back()->with('error', 'No se pudo restablecer la contraseña.');
            }
        }

        return $response ?? redirect()->back()->with('error', 'No se pudo procesar la solicitud.');
    }

    public function createUser(Persona $persona): RedirectResponse
    {
        $response = null;

        if ($persona->user) {
            $response = redirect()->back()->with('error', 'La persona ya tiene un usuario asociado.');
        } elseif (empty($persona->email)) {
            $response = redirect()->back()->with('error', 'La persona no tiene correo registrado.');
        } elseif (empty($persona->numero_documento)) {
            $response = redirect()->back()->with('error', 'La persona no tiene número de documento registrado.');
        } else {
            try {
                $this->personaService->crearUsuarioParaPersona($persona);
                $response = redirect()->back()->with('success', 'Usuario creado correctamente con rol VISITANTE.');
            } catch (\Throwable $e) {
                $response = redirect()
                    ->back()
                    ->with('error', 'No se pudo crear el usuario. '.($e->getMessage() ?? ''));
            }
        }

        return $response ?? redirect()->back()->with('error', 'No se pudo procesar la solicitud.');
    }
}
