<?php

namespace App\Console\Commands\Concerns\ValidarFichasCaracterizacion;

use App\Models\FichaCaracterizacion;
use App\Services\FichaCaracterizacionValidationService;

trait HandlesValidarFichasCaracterizacionValidationActions
{
    protected FichaCaracterizacionValidationService $validationService;

    protected function validarFichaEspecifica($fichaId, $corregirErrores = false): void
    {
        $this->info("📋 Validando ficha ID: {$fichaId}");

        $ficha = FichaCaracterizacion::find($fichaId);
        if (! $ficha) {
            $this->error("❌ No se encontró la ficha con ID: {$fichaId}");

            return;
        }

        $this->mostrarInformacionFicha($ficha);

        $datos = $ficha->toArray();
        $resultado = $this->validationService->validarFichaCompleta($datos, $ficha->id);

        $this->mostrarResultadoValidacion($resultado);

        if ($corregirErrores && ! $resultado['valido']) {
            $this->intentarCorregirErrores($resultado['errores']);
        }
    }

    protected function validarTodasLasFichas($corregirErrores = false): void
    {
        $this->info('📋 Validando todas las fichas de caracterización...');

        $fichas = FichaCaracterizacion::where('status', true)->get();
        $totalFichas = $fichas->count();
        $fichasValidas = 0;
        $fichasConErrores = 0;
        $fichasConAdvertencias = 0;

        $this->info("Total de fichas a validar: {$totalFichas}");

        $bar = $this->output->createProgressBar($totalFichas);
        $bar->start();

        foreach ($fichas as $ficha) {
            $datos = $ficha->toArray();
            $resultado = $this->validationService->validarFichaCompleta($datos, $ficha->id);

            if ($resultado['valido']) {
                $fichasValidas++;
            } else {
                $fichasConErrores++;
            }

            if (count($resultado['advertencias']) > 0) {
                $fichasConAdvertencias++;
            }

            if ($corregirErrores && ! $resultado['valido']) {
                $this->intentarCorregirErrores($resultado['errores']);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if ($totalFichas === 0) {
            $this->warn('No hay fichas activas para validar.');

            return;
        }

        $this->info('📊 Resumen de la validación:');
        $this->table(
            ['Estado', 'Cantidad', 'Porcentaje'],
            [
                ['✅ Válidas', $fichasValidas, round(($fichasValidas / $totalFichas) * 100, 2).'%'],
                ['❌ Con Errores', $fichasConErrores, round(($fichasConErrores / $totalFichas) * 100, 2).'%'],
                ['⚠️ Con Advertencias', $fichasConAdvertencias, round(($fichasConAdvertencias / $totalFichas) * 100, 2).'%'],
            ]
        );
    }

    protected function intentarCorregirErrores($errores): void
    {
        $this->warn('🔧 Intentando corregir errores automáticamente...');

        foreach ($errores as $error) {
            $this->warn("   - {$error}");
        }

        $this->info('💡 Las correcciones automáticas están en desarrollo.');
    }
}
