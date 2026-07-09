<?php

namespace App\Services;

use App\Repositories\AprendizRepository;
use App\Repositories\AsistenciaAprendizRepository;
use App\Repositories\FichaRepository;
use App\Services\Concerns\Reporte\HandlesReporteAprendicesActions;
use App\Services\Concerns\Reporte\HandlesReporteAsistenciaActions;
use App\Services\Concerns\Reporte\HandlesReporteExcelHelpers;
use App\Services\Concerns\Reporte\HandlesReportePdfHelpers;
use App\Services\Concerns\Reporte\HandlesReporteResumenHelpers;

class ReporteService
{
    use HandlesReporteAprendicesActions;
    use HandlesReporteAsistenciaActions;
    use HandlesReporteExcelHelpers;
    use HandlesReportePdfHelpers;
    use HandlesReporteResumenHelpers;

    protected AsistenciaAprendizRepository $asistenciaRepo;

    protected AprendizRepository $aprendizRepo;

    protected FichaRepository $fichaRepo;

    public function __construct(
        AsistenciaAprendizRepository $asistenciaRepo,
        AprendizRepository $aprendizRepo,
        FichaRepository $fichaRepo
    ) {
        $this->asistenciaRepo = $asistenciaRepo;
        $this->aprendizRepo = $aprendizRepo;
        $this->fichaRepo = $fichaRepo;
    }
}
