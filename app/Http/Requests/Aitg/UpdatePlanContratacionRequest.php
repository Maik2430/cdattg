<?php

namespace App\Http\Requests\Aitg;

class UpdatePlanContratacionRequest extends StorePlanContratacionRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('EDITAR PLAN CONTRATACION') ?? false;
    }
}
