<?php

namespace App\Services\Complementarios\Sofia;

use App\Services\Concerns\Complementarios\SofiaParametros\HandlesSofiaParametrosCacheHelpers;
use App\Services\Concerns\Complementarios\SofiaParametros\HandlesSofiaParametrosCreationHelpers;
use App\Services\Concerns\Complementarios\SofiaParametros\HandlesSofiaParametrosGetterActions;

class SofiaParametrosHelper
{
    use HandlesSofiaParametrosCacheHelpers;
    use HandlesSofiaParametrosCreationHelpers;
    use HandlesSofiaParametrosGetterActions;

    private const CACHE_KEY = 'sofia_parametros_ids';

    private const CACHE_TTL = 3600;
}
