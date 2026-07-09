<?php

namespace App\Http\Requests\Concerns\CreateInstructor;

trait HandlesCreateInstructorAuthorization
{
    public function authorize(): bool
    {
        return true;
    }
}
