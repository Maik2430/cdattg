<?php

namespace App\Services;

use App\Core\Traits\HasCache;
use App\Repositories\AprendizRepository;
use App\Repositories\AsistenciaAprendizRepository;
use App\Repositories\FichaRepository;
use App\Services\Concerns\Estadisticas\HandlesEstadisticasAsistenciaActions;
use App\Services\Concerns\Estadisticas\HandlesEstadisticasDashboardActions;

class EstadisticasService
{
    use HandlesEstadisticasAsistenciaActions;
    use HandlesEstadisticasDashboardActions;
    use HasCache;

    protected AprendizRepository $aprendizRepo;

    protected FichaRepository $fichaRepo;

    protected AsistenciaAprendizRepository $asistenciaRepo;

    protected PersonaIngresoSalidaService $personaIngresoSalidaService;

    public function __construct(
        AprendizRepository $aprendizRepo,
        FichaRepository $fichaRepo,
        AsistenciaAprendizRepository $asistenciaRepo,
        PersonaIngresoSalidaService $personaIngresoSalidaService
    ) {
        $this->aprendizRepo = $aprendizRepo;
        $this->fichaRepo = $fichaRepo;
        $this->asistenciaRepo = $asistenciaRepo;
        $this->personaIngresoSalidaService = $personaIngresoSalidaService;
        $this->cacheType = 'estadisticas';
    }
}
