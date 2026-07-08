<?php

namespace App\Policies\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\User;

trait DocumentoPolicy
{
    /**
     * Determine whether the user can manage instructor documents.
     * Los instructores pueden gestionar sus propios documentos.
     */
    public function gestionarDocumentos(User $user, Instructor $instructor): bool
    {
        // Verificar permiso específico
        if (! $user->can('GESTIONAR DOCUMENTOS INSTRUCTOR')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, solo puede gestionar sus propios documentos
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->esElMismoInstructor($user, $instructor);
        }

        return true;
    }

    /**
     * Determine whether the user can upload documents.
     */
    public function subirDocumento(User $user, Instructor $instructor): bool
    {
        return $this->gestionarDocumentos($user, $instructor);
    }

    /**
     * Determine whether the user can download documents.
     */
    public function descargarDocumento(User $user, Instructor $instructor): bool
    {
        return $this->gestionarDocumentos($user, $instructor);
    }

    /**
     * Determine whether the user can delete documents.
     */
    public function eliminarDocumento(User $user, Instructor $instructor): bool
    {
        return $this->gestionarDocumentos($user, $instructor);
    }
}
