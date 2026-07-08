<?php

namespace App\Models\Concerns\Persona;

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

trait BuildsPersonaAttributes
{
    /**
     * Accesor para obtener el nombre completo de la persona.
     */
    public function getNombreCompletoAttribute(): string
    {
        $nombres = [
            $this->primer_nombre,
            $this->segundo_nombre,
            $this->primer_apellido,
            $this->segundo_apellido,
        ];

        return trim(implode(' ', array_filter($nombres)));
    }

    /**
     * Accesor para calcular la edad a partir de la fecha de nacimiento.
     */
    public function getEdadAttribute(): int
    {
        return Carbon::parse($this->fecha_nacimiento)->age;
    }

    /**
     * Accesor para obtener el email.
     * Prioriza el email del usuario relacionado si existe, sino usa el de la persona.
     */
    public function getEmailAttribute(?string $value): ?string
    {
        if (! Schema::hasTable('users')) {
            return $value;
        }

        if ($this->relationLoaded('user') && $this->user) {
            return $this->user->email;
        }

        if (! $this->relationLoaded('user')) {
            try {
                $user = $this->user;
                if ($user) {
                    return $user->email;
                }
            } catch (\Exception $e) {
                return $value;
            }
        }

        return $value;
    }
}
