<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\StoreInstructor\HandlesStoreInstructorAttributes;
use App\Http\Requests\Concerns\StoreInstructor\HandlesStoreInstructorAuthorization;
use App\Http\Requests\Concerns\StoreInstructor\HandlesStoreInstructorMessages;
use App\Http\Requests\Concerns\StoreInstructor\HandlesStoreInstructorRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreInstructorRequest extends FormRequest
{
    use HandlesStoreInstructorAttributes;
    use HandlesStoreInstructorAuthorization;
    use HandlesStoreInstructorMessages;
    use HandlesStoreInstructorRules;
}
