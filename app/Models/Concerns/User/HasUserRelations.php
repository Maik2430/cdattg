<?php

namespace App\Models\Concerns\User;

use App\Models\Ambiente;
use App\Models\Bloque;
use App\Models\EntradaSalida;
use App\Models\FichaCaracterizacion;
use App\Models\Parametro;
use App\Models\Persona;
use App\Models\Piso;
use App\Models\Sede;

trait HasUserRelations
{
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function entradaSalida()
    {
        return $this->hasMany(EntradaSalida::class);
    }

    public function bloqueCreated()
    {
        return $this->hasMany(Bloque::class, 'user_create_id');
    }

    public function bloqueEdited()
    {
        return $this->hasMany(Bloque::class, 'user_edit_id');
    }

    public function sedeCreated()
    {
        return $this->hasMany(Sede::class);
    }

    public function sedeEdited()
    {
        return $this->hasMany(Sede::class);
    }

    public function pisoCreated()
    {
        return $this->hasMany(Piso::class, 'user_create_id');
    }

    public function pisoEdited()
    {
        return $this->hasMany(Piso::class, 'user_edit_id');
    }

    public function ambienteCreated()
    {
        return $this->hasMany(Ambiente::class, 'user_create_id');
    }

    public function ambienteEdited()
    {
        return $this->hasMany(Ambiente::class, 'user_edit_id');
    }

    public function parametrosCreated()
    {
        return $this->hasMany(Parametro::class, 'user_create_id');
    }

    public function fichaCaracterizacionCreate()
    {
        return $this->hasMany(FichaCaracterizacion::class);
    }

    public function fichaCaracerizacionEdit()
    {
        return $this->hasMany(FichaCaracterizacion::class);
    }
}
