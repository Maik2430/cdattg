<?php

namespace App\Models\Concerns\Instructor;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait BuildsInstructorPersonaAttributes
{
    /**
     * Obtener el nombre completo del instructor.
     */
    public function getNombreCompletoAttribute(): string
    {
        $persona = $this->persona;
        if (! $persona) {
            return 'Sin nombre';
        }

        $nombre = $persona->primer_nombre;
        if ($persona->segundo_nombre) {
            $nombre .= ' '.$persona->segundo_nombre;
        }
        $nombre .= ' '.$persona->primer_apellido;
        if ($persona->segundo_apellido) {
            $nombre .= ' '.$persona->segundo_apellido;
        }

        return $nombre;
    }

    /**
     * Obtener el número de documento del instructor.
     */
    public function getNumeroDocumentoAttribute(): string
    {
        return $this->persona ? $this->persona->numero_documento : 'Sin documento';
    }

    /**
     * Obtener el email del instructor.
     */
    public function getEmailAttribute(): string
    {
        return $this->persona ? $this->persona->email : 'Sin email';
    }

    /**
     * Calcular la edad del instructor.
     */
    public function getEdadAttribute(): int
    {
        if (! $this->persona || ! $this->persona->fecha_nacimiento) {
            return 0;
        }

        try {
            $fechaNacimiento = $this->persona->fecha_nacimiento;

            if ($fechaNacimiento instanceof Carbon) {
                return $fechaNacimiento->age;
            }

            if (is_string($fechaNacimiento)) {
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fechaNacimiento)) {
                    return Carbon::createFromFormat('d/m/Y', $fechaNacimiento)->age;
                }

                return Carbon::parse($fechaNacimiento)->age;
            }

            return Carbon::parse($fechaNacimiento)->age;
        } catch (\Exception $e) {
            Log::warning('Error al calcular edad del instructor', [
                'instructor_id' => $this->id,
                'fecha_nacimiento' => $this->persona->fecha_nacimiento ?? null,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }
}
