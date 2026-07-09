<?php

namespace App\Http\Controllers\Concerns\Login;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait HandlesLoginApiActions
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // $token = $user->createToken('Token Name')->plainTextToken; // Comentado para evitar error

            // Buscar la persona asociada
            $personaD = Persona::find($user->persona_id);
            if (! $personaD) {
                return response()->json(['error' => 'No se encontró la persona asociada'], 404);
            }

            // Construcción de datos de la persona
            $persona = [
                'id' => $personaD->id,
                'tipo_documento' => optional($personaD->tipoDocumento)->name ?? null,
                'numero_documento' => $personaD->numero_documento,
                'primer_nombre' => $personaD->primer_nombre,
                'segundo_nombre' => $personaD->segundo_nombre,
                'primer_apellido' => $personaD->primer_apellido,
                'segundo_apellido' => $personaD->segundo_apellido,
                'fecha_de_nacimiento' => $personaD->fecha_de_nacimiento,
                'genero' => optional($personaD->tipoGenero)->name,
                'email' => $personaD->email,
                'created_at' => $personaD->created_at,
                'updated_at' => $personaD->updated_at,
                'instructor_id' => optional($personaD->instructor)->id,
                'regional_id' => optional(optional($personaD->instructor)->regional)->id,
            ];

            return response()->json(['user' => $user, 'persona' => $persona], 200);
        }

        return response()->json(['error' => 'Credenciales incorrectas'], 401);
    }
}
