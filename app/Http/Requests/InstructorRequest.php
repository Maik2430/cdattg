<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\Instructor\HandlesInstructorAuthorization;
use App\Http\Requests\Concerns\Instructor\HandlesInstructorBusinessRulesHelpers;
use App\Http\Requests\Concerns\Instructor\HandlesInstructorEspecialidadesHelpers;
use App\Http\Requests\Concerns\Instructor\HandlesInstructorMessages;
use App\Http\Requests\Concerns\Instructor\HandlesInstructorRules;
use App\Http\Requests\Concerns\Instructor\HandlesInstructorWithValidator;
use Illuminate\Foundation\Http\FormRequest;

class InstructorRequest extends FormRequest
{
    use HandlesInstructorAuthorization;
    use HandlesInstructorBusinessRulesHelpers;
    use HandlesInstructorEspecialidadesHelpers;
    use HandlesInstructorMessages;
    use HandlesInstructorRules;
    use HandlesInstructorWithValidator;
}
