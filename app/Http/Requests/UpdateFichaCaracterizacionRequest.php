<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\UpdateFichaCaracterizacion\HandlesUpdateFichaCaracterizacionAttributes;
use App\Http\Requests\Concerns\UpdateFichaCaracterizacion\HandlesUpdateFichaCaracterizacionAuthorization;
use App\Http\Requests\Concerns\UpdateFichaCaracterizacion\HandlesUpdateFichaCaracterizacionMessages;
use App\Http\Requests\Concerns\UpdateFichaCaracterizacion\HandlesUpdateFichaCaracterizacionPrepareForValidation;
use App\Http\Requests\Concerns\UpdateFichaCaracterizacion\HandlesUpdateFichaCaracterizacionRules;
use App\Http\Requests\Concerns\UpdateFichaCaracterizacion\HandlesUpdateFichaCaracterizacionWithValidator;
use App\Traits\ValidacionesSena;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFichaCaracterizacionRequest extends FormRequest
{
    use HandlesUpdateFichaCaracterizacionAttributes;
    use HandlesUpdateFichaCaracterizacionAuthorization;
    use HandlesUpdateFichaCaracterizacionMessages;
    use HandlesUpdateFichaCaracterizacionPrepareForValidation;
    use HandlesUpdateFichaCaracterizacionRules;
    use HandlesUpdateFichaCaracterizacionWithValidator;
    use ValidacionesSena;
}
