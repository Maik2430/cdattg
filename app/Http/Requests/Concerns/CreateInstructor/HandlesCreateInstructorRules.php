<?php

namespace App\Http\Requests\Concerns\CreateInstructor;

trait HandlesCreateInstructorRules
{
    use HandlesCreateInstructorBasicRules;
    use HandlesCreateInstructorExtendedRules;

    public function rules(): array
    {
        return array_merge(
            $this->createInstructorBasicRules(),
            $this->createInstructorExtendedRules()
        );
    }
}
