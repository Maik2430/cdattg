<?php

namespace App\Policies\Concerns\GuiaAprendizaje;

use App\Models\GuiasAprendizaje;
use App\Models\User;

trait ComentariosPolicy
{
    /**
     * Determine whether the user can manage guía comments.
     * Los usuarios pueden gestionar comentarios según sus permisos.
     */
    public function gestionarComentarios(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        // Verificar permiso específico
        if (! $user->can('EDITAR GUIA APRENDIZAJE')) {
            return false;
        }

        // Super administradores y administradores tienen acceso total
        if ($user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return true;
        }

        // Si el usuario es instructor, verificar permisos específicos
        if ($user->hasRole('INSTRUCTOR')) {
            return $this->instructorPuedeEditarGuia($user, $guiaAprendizaje);
        }

        return true;
    }

    /**
     * Determine whether the user can add comment.
     */
    public function agregarComentario(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarComentarios($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can edit comment.
     */
    public function editarComentario(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarComentarios($user, $guiaAprendizaje);
    }

    /**
     * Determine whether the user can delete comment.
     */
    public function eliminarComentario(User $user, GuiasAprendizaje $guiaAprendizaje): bool
    {
        return $this->gestionarComentarios($user, $guiaAprendizaje);
    }
}
