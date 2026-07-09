<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\CreateInstructor\HandlesCreateInstructorAuthorization;
use App\Http\Requests\Concerns\CreateInstructor\HandlesCreateInstructorMessages;
use App\Http\Requests\Concerns\CreateInstructor\HandlesCreateInstructorRules;
use App\Http\Requests\Concerns\CreateInstructor\HandlesCreateInstructorWithValidator;
use Illuminate\Foundation\Http\FormRequest;

class CreateInstructorRequest extends FormRequest
{
    use HandlesCreateInstructorAuthorization;
    use HandlesCreateInstructorMessages;
    use HandlesCreateInstructorRules;
    use HandlesCreateInstructorWithValidator;
}
