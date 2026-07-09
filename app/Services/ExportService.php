<?php

namespace App\Services;

use App\Services\Concerns\Export\HandlesExportCsvActions;
use App\Services\Concerns\Export\HandlesExportExcelActions;
use App\Services\Concerns\Export\HandlesExportJsonActions;

class ExportService
{
    use HandlesExportCsvActions;
    use HandlesExportExcelActions;
    use HandlesExportJsonActions;
}
