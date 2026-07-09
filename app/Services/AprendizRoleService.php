<?php

namespace App\Services;

use App\Services\Concerns\AprendizRole\HandlesAprendizRoleEnsureActions;
use App\Services\Concerns\AprendizRole\HandlesAprendizRoleStatisticsActions;
use App\Services\Concerns\AprendizRole\HandlesAprendizRoleSyncActions;
use App\Services\Concerns\AprendizRole\HandlesAprendizRoleUserHelpers;
use App\Services\Concerns\AprendizRole\HandlesAprendizRoleValidationActions;

class AprendizRoleService
{
    use HandlesAprendizRoleEnsureActions;
    use HandlesAprendizRoleStatisticsActions;
    use HandlesAprendizRoleSyncActions;
    use HandlesAprendizRoleUserHelpers;
    use HandlesAprendizRoleValidationActions;
}
