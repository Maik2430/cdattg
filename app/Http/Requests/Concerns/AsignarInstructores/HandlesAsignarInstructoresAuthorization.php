<?php

namespace App\Http\Requests\Concerns\AsignarInstructores;

trait HandlesAsignarInstructoresAuthorization
{
    public function authorize(): bool
    {
        return $this->user()->can('EDITAR INSTRUCTOR') || $this->user()->can('CREAR INSTRUCTOR');
    }
}
