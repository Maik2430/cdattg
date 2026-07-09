<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\RefactorSonarQube\HandlesRefactorSonarQubeAnalysisActions;
use App\Console\Commands\Concerns\RefactorSonarQube\HandlesRefactorSonarQubeExecutionActions;
use App\Console\Commands\Concerns\RefactorSonarQube\HandlesRefactorSonarQubeFileDiscoveryHelpers;
use App\Console\Commands\Concerns\RefactorSonarQube\HandlesRefactorSonarQubeReportActions;
use Illuminate\Console\Command;

class RefactorSonarQubeCommand extends Command
{
    use HandlesRefactorSonarQubeAnalysisActions;
    use HandlesRefactorSonarQubeExecutionActions;
    use HandlesRefactorSonarQubeFileDiscoveryHelpers;
    use HandlesRefactorSonarQubeReportActions;

    protected $signature = 'refactor:sonarqube
                            {--dry-run : Ejecutar sin aplicar cambios}
                            {--path=app : Ruta específica a analizar}';

    protected $description = 'Analiza y corrige problemas de mantenibilidad detectados por SonarQube';

    private array $stats = [
        'archivos_analizados' => 0,
        'errores_encontrados' => 0,
        'errores_corregidos' => 0,
        'archivos_modificados' => [],
    ];
}
