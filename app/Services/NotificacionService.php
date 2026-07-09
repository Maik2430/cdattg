<?php

namespace App\Services;

use App\Services\Concerns\Notificacion\HandlesNotificacionAdminActions;
use App\Services\Concerns\Notificacion\HandlesNotificacionAprendizActions;
use App\Services\Concerns\Notificacion\HandlesNotificacionAsistenciaActions;
use App\Services\Concerns\Notificacion\HandlesNotificacionInstructorActions;

class NotificacionService
{
    use HandlesNotificacionAdminActions;
    use HandlesNotificacionAprendizActions;
    use HandlesNotificacionAsistenciaActions;
    use HandlesNotificacionInstructorActions;
}
