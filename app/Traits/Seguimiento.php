<?php

namespace App\Traits;

use App\Models\User;


trait Seguimiento
{
    
    // Relación con el usuario que creó el registro
    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }
    
    // Relación con el usuario que actualizó el registro por última vez
    public function userUpdate()
    {
        return $this->belongsTo(User::class, 'user_update_id');
    }
    
    // Alias de userCreate() para compatibilidad
    public function creador()
    {
        return $this->userCreate();
    }

    // Alias de userUpdate() para compatibilidad
    public function actualizador()
    {
        return $this->userUpdate();
    }

    // Relación con el usuario que eliminó el registro
    public function userDelete()
    {
        return $this->belongsTo(User::class, 'user_delete_id');
    }

    // Alias de userDelete() para compatibilidad
    public function eliminador()
    {
        return $this->userDelete();
    }
}
