<?php

namespace App\Http\Controllers\Concerns\Persona;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait HandlesPersonaCrudReadActions
{
    use HandlesPersonaFormDataHelpers;
    use HandlesPersonaShowHelpers;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('personas.index');
    }

    /**
     * Redirige al usuario a su propio perfil.
     * Solo accesible para usuarios con permiso 'VER PERFIL'.
     */
    public function miPerfil()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403, self::ERROR_USER_NOT_RESOLVED);
        }

        if (! $user->can(self::PERMISSION_VIEW_PROFILE)) {
            abort(403, 'No tienes permiso para ver tu perfil.');
        }

        if (! $user->persona_id) {
            return redirect()->route('verificarLogin')
                ->with('error', 'No se encontró información de persona para este usuario.');
        }

        return redirect()->route('personas.show', $user->persona_id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('personas.create', $this->loadPersonaCreateFormData());
    }

    /**
     * Display the specified resource.
     */
    public function show(Persona $persona)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403, self::ERROR_USER_NOT_RESOLVED);
        }

        if ($user->can(self::PERMISSION_VIEW_PERSON)) {
            return $this->renderPersonaShowFullAccess($persona, $user);
        }

        if ($user->can(self::PERMISSION_VIEW_PROFILE)) {
            if ($user->persona_id !== $persona->id) {
                abort(403, 'No tienes permiso para ver este perfil. Solo puedes ver tu propio perfil.');
            }

            return $this->renderPersonaShowProfileOnly($persona);
        }

        abort(403, 'No tienes permiso para ver este perfil.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Persona $persona)
    {
        return view('personas.edit', $this->loadPersonaEditFormData($persona));
    }
}
