<?php

namespace App\Models\Concerns\Persona;

use App\Models\Parametro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait SyncsPersonaLifecycle
{
    protected static function bootSyncsPersonaLifecycle(): void
    {
        static::creating(function ($persona) {
            // Establecer estado_sofia por defecto si no se especifica (NO REGISTRADO = 277)
            if (! isset($persona->estado_sofia) || $persona->estado_sofia === null) {
                $noRegistrado = Parametro::find(277);
                if ($noRegistrado) {
                    $persona->estado_sofia = $noRegistrado->id;
                }
            }
        });

        static::saving(function ($persona) {
            $persona->primer_nombre = strtoupper($persona->primer_nombre);
            $persona->segundo_nombre = strtoupper($persona->segundo_nombre);
            $persona->primer_apellido = strtoupper($persona->primer_apellido);
            $persona->segundo_apellido = strtoupper($persona->segundo_apellido);
            $persona->direccion = strtoupper($persona->direccion);

            // Sincronizar email con el usuario relacionado si existe
            if ($persona->isDirty('email') && Schema::hasTable('users')) {
                $newEmail = $persona->getAttributes()['email'] ?? $persona->getOriginal('email');

                try {
                    $user = DB::table('users')->where('persona_id', $persona->id)->first();
                    if ($user) {
                        DB::table('users')
                            ->where('persona_id', $persona->id)
                            ->update(['email' => $newEmail]);
                    }
                } catch (\Exception $e) {
                    // Si hay error (tabla no existe, etc.), simplemente continuar
                }
            }
        });

        static::deleting(function ($persona) {
            if (Schema::hasTable('users')) {
                try {
                    if ($persona->user) {
                        $persona->user->delete();
                    }
                } catch (\Exception $e) {
                    // Si hay error, simplemente continuar
                }
            }
        });
    }
}
