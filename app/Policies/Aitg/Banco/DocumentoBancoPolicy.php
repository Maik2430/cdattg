<?php

namespace App\Policies\Aitg\Banco;

use App\Models\Aitg\Banco\DocumentoBanco;
use App\Models\User;

class DocumentoBancoPolicy
{
    public function view(User $user, DocumentoBanco $documento): bool
    {
        if ($user->can('VALIDAR DOCUMENTO BANCO AITG')) {
            return true;
        }

        return $documento->solicitud->user_id === $user->id
            && $user->can('VER BANCO INSTRUCTOR AITG');
    }

    public function delete(User $user, DocumentoBanco $documento): bool
    {
        return $documento->solicitud->user_id === $user->id
            && $user->can('ELIMINAR DOCUMENTO BANCO AITG');
    }
}
