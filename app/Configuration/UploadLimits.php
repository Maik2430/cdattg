<?php

namespace App\Configuration;

use App\Configuration\Concerns\DefinesUploadLimitsConstants;
use App\Configuration\Concerns\HandlesUploadLimitsPhpConfigHelpers;
use App\Configuration\Concerns\HandlesUploadLimitsValidationHelpers;

/**
 * Configuración centralizada de límites de carga de archivos.
 *
 * IMPORTANTE: Estos límites deben estar sincronizados con la configuración de PHP:
 * - upload_max_filesize >= 8M
 * - post_max_size >= 8M
 * - memory_limit >= 128M
 * - max_execution_time >= 300 (5 minutos)
 */
final class UploadLimits
{
    use DefinesUploadLimitsConstants;
    use HandlesUploadLimitsPhpConfigHelpers;
    use HandlesUploadLimitsValidationHelpers;
}
