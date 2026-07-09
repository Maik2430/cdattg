<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns\Complementarios\CreateAspirante;

trait HandlesCreateAspiranteAuthorization
{
    public function authorize(): bool
    {
        return true;
    }
}
