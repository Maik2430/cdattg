<?php

namespace App\Http\Requests\Concerns\Instructor;

trait HandlesInstructorWithValidator
{
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateBusinessRules($validator);
        });
    }
}
