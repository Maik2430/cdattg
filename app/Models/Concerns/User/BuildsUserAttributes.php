<?php

namespace App\Models\Concerns\User;

trait BuildsUserAttributes
{
    public function getNameAttribute()
    {
        if ($this->persona) {
            $nombre = trim($this->persona->primer_nombre.' '.$this->persona->segundo_nombre);
            $apellido = trim($this->persona->primer_apellido.' '.$this->persona->segundo_apellido);

            return trim($nombre.' '.$apellido);
        }

        return 'Usuario sin nombre';
    }
}
