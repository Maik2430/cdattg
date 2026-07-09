<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\AsignarInstructores\HandlesAsignarInstructoresAuthorization;
use App\Http\Requests\Concerns\AsignarInstructores\HandlesAsignarInstructoresConflictAsignacionesExistentesHelpers;
use App\Http\Requests\Concerns\AsignarInstructores\HandlesAsignarInstructoresConflictFechasHelpers;
use App\Http\Requests\Concerns\AsignarInstructores\HandlesAsignarInstructoresConflictMismaFichaFormularioHelpers;
use App\Http\Requests\Concerns\AsignarInstructores\HandlesAsignarInstructoresConflictOtrosInstructorHelpers;
use App\Http\Requests\Concerns\AsignarInstructores\HandlesAsignarInstructoresHorasValidationHelpers;
use App\Http\Requests\Concerns\AsignarInstructores\HandlesAsignarInstructoresInstructorFieldValidationHelpers;
use App\Http\Requests\Concerns\AsignarInstructores\HandlesAsignarInstructoresMessages;
use App\Http\Requests\Concerns\AsignarInstructores\HandlesAsignarInstructoresPrepareForValidation;
use App\Http\Requests\Concerns\AsignarInstructores\HandlesAsignarInstructoresRules;
use App\Http\Requests\Concerns\AsignarInstructores\HandlesAsignarInstructoresSenaValidationHelpers;
use App\Http\Requests\Concerns\AsignarInstructores\HandlesAsignarInstructoresWithValidator;
use Illuminate\Foundation\Http\FormRequest;

class AsignarInstructoresRequest extends FormRequest
{
    use HandlesAsignarInstructoresAuthorization;
    use HandlesAsignarInstructoresConflictAsignacionesExistentesHelpers;
    use HandlesAsignarInstructoresConflictFechasHelpers;
    use HandlesAsignarInstructoresConflictMismaFichaFormularioHelpers;
    use HandlesAsignarInstructoresConflictOtrosInstructorHelpers;
    use HandlesAsignarInstructoresHorasValidationHelpers;
    use HandlesAsignarInstructoresInstructorFieldValidationHelpers;
    use HandlesAsignarInstructoresMessages;
    use HandlesAsignarInstructoresPrepareForValidation;
    use HandlesAsignarInstructoresRules;
    use HandlesAsignarInstructoresSenaValidationHelpers;
    use HandlesAsignarInstructoresWithValidator;
}
