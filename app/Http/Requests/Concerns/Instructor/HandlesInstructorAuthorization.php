<?php

namespace App\Http\Requests\Concerns\Instructor;

trait HandlesInstructorAuthorization
{
    public function authorize(): bool
    {
        return true;
    }
}
