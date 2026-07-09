<?php

namespace App\Http\Requests\Concerns\StoreInstructor;

trait HandlesStoreInstructorAuthorization
{
    public function authorize(): bool
    {
        return true;
    }
}
