<?php

namespace App\Models\Concerns\User;

use Illuminate\Support\Facades\DB;

trait SyncsUserEmailWithPersona
{
    /**
     * Boot del modelo para sincronizar email con persona
     */
    protected static function bootSyncsUserEmailWithPersona(): void
    {
        static::saving(function ($user) {
            if ($user->isDirty('email') && $user->persona_id) {
                DB::table('personas')
                    ->where('id', $user->persona_id)
                    ->update(['email' => $user->email]);
            }
        });
    }
}
