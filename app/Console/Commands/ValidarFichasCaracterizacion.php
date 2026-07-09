<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\ValidarFichasCaracterizacion\HandlesValidarFichasCaracterizacionDisplayActions;
use App\Console\Commands\Concerns\ValidarFichasCaracterizacion\HandlesValidarFichasCaracterizacionValidationActions;
use App\Services\FichaCaracterizacionValidationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ValidarFichasCaracterizacion extends Command
{
    use HandlesValidarFichasCaracterizacionDisplayActions;
    use HandlesValidarFichasCaracterizacionValidationActions;

    protected $signature = 'fichas:validar {--id= : ID específico de la ficha a validar} {--all : Validar todas las fichas} {--fix : Intentar corregir errores automáticamente}';

    protected $description = 'Valida las fichas de caracterización según las reglas de negocio del SENA';

    public function __construct()
    {
        parent::__construct();
        $this->validationService = new FichaCaracterizacionValidationService;
    }

    public function handle(): int
    {
        $this->info('🔍 Iniciando validación de fichas de caracterización...');

        $fichaId = $this->option('id');
        $validarTodas = $this->option('all');
        $corregirErrores = $this->option('fix');

        try {
            if ($fichaId) {
                $this->validarFichaEspecifica($fichaId, $corregirErrores);
            } elseif ($validarTodas) {
                $this->validarTodasLasFichas($corregirErrores);
            } else {
                $this->error('Debe especificar --id=<ID> para validar una ficha específica o --all para validar todas las fichas.');

                return 1;
            }

            $this->info('✅ Validación completada exitosamente.');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error durante la validación: '.$e->getMessage());
            Log::error('Error en comando de validación de fichas', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 1;
        }
    }
}
