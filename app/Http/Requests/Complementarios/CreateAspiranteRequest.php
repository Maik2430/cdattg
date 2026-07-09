<?php

declare(strict_types=1);

namespace App\Http\Requests\Complementarios;

use App\Http\Requests\Concerns\Complementarios\CreateAspirante\HandlesCreateAspiranteAuthorization;
use App\Http\Requests\Concerns\Complementarios\CreateAspirante\HandlesCreateAspiranteMessages;
use App\Http\Requests\Concerns\Complementarios\CreateAspirante\HandlesCreateAspirantePrepareForValidation;
use App\Http\Requests\Concerns\Complementarios\CreateAspirante\HandlesCreateAspirantePrepareForValidationHelpers;
use App\Http\Requests\Concerns\Complementarios\CreateAspirante\HandlesCreateAspiranteRules;
use Illuminate\Foundation\Http\FormRequest;

class CreateAspiranteRequest extends FormRequest
{
    use HandlesCreateAspiranteAuthorization;
    use HandlesCreateAspiranteMessages;
    use HandlesCreateAspirantePrepareForValidation;
    use HandlesCreateAspirantePrepareForValidationHelpers;
    use HandlesCreateAspiranteRules;
}
