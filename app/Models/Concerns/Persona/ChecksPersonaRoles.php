<?php

namespace App\Models\Concerns\Persona;

trait ChecksPersonaRoles
{
    public function esProveedor(): bool
    {
        return $this->proveedor()->exists();
    }

    /**
     * Verifica si la persona es un aprendiz.
     */
    public function esAprendiz(): bool
    {
        return $this->aprendiz()->exists();
    }

    /**
     * Verifica si la persona es un aprendiz activo.
     */
    public function esAprendizActivo(): bool
    {
        return $this->aprendiz()->where('estado', 1)->exists();
    }

    /**
     * Verifica si la persona tiene un rol específico.
     */
    public function hasRole(string $role): bool
    {
        if (! $this->user) {
            return false;
        }

        return $this->user->hasRole(strtoupper($role));
    }

    /**
     * Verifica si la persona es instructor.
     */
    public function esInstructor(): bool
    {
        return $this->instructor()->exists();
    }

    /**
     * Verifica si la persona tiene el rol de APRENDIZ.
     */
    public function tieneRolAprendiz(): bool
    {
        if (! $this->user) {
            return false;
        }

        return $this->user->hasRole('APRENDIZ');
    }
}
