<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\StoreFichaCaracterizacion\HandlesStoreFichaCaracterizacionAttributes;
use App\Http\Requests\Concerns\StoreFichaCaracterizacion\HandlesStoreFichaCaracterizacionAuthorization;
use App\Http\Requests\Concerns\StoreFichaCaracterizacion\HandlesStoreFichaCaracterizacionDiasFormacionValidationHelpers;
use App\Http\Requests\Concerns\StoreFichaCaracterizacion\HandlesStoreFichaCaracterizacionMessages;
use App\Http\Requests\Concerns\StoreFichaCaracterizacion\HandlesStoreFichaCaracterizacionPrepareForValidation;
use App\Http\Requests\Concerns\StoreFichaCaracterizacion\HandlesStoreFichaCaracterizacionRules;
use App\Http\Requests\Concerns\StoreFichaCaracterizacion\HandlesStoreFichaCaracterizacionWithValidator;
use App\Traits\ValidacionesSena;
use Illuminate\Foundation\Http\FormRequest;

class StoreFichaCaracterizacionRequest extends FormRequest
{
    use HandlesStoreFichaCaracterizacionAttributes;
    use HandlesStoreFichaCaracterizacionAuthorization;
    use HandlesStoreFichaCaracterizacionDiasFormacionValidationHelpers;
    use HandlesStoreFichaCaracterizacionMessages;
    use HandlesStoreFichaCaracterizacionPrepareForValidation;
    use HandlesStoreFichaCaracterizacionRules;
    use HandlesStoreFichaCaracterizacionWithValidator;
    use ValidacionesSena;
}
