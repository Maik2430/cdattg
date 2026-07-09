<?php

namespace App\Core\Services;

use App\Core\Services\Concerns\Cache\DefinesCacheConstants;
use App\Core\Services\Concerns\Cache\HandlesCacheInvalidationActions;
use App\Core\Services\Concerns\Cache\HandlesCacheKeyHelpers;
use App\Core\Services\Concerns\Cache\HandlesCacheRememberActions;
use App\Core\Services\Concerns\Cache\HandlesCacheWarmupActions;

class CacheService
{
    use DefinesCacheConstants;
    use HandlesCacheInvalidationActions;
    use HandlesCacheKeyHelpers;
    use HandlesCacheRememberActions;
    use HandlesCacheWarmupActions;
}
