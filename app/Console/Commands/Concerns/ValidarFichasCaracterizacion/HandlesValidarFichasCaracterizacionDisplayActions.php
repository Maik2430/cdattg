<?php

namespace App\Console\Commands\Concerns\ValidarFichasCaracterizacion;

trait HandlesValidarFichasCaracterizacionDisplayActions
{
    protected function mostrarInformacionFicha($ficha): void
    {
        $this->info("📄 Ficha: {$ficha->ficha}");
        $this->info('📚 Programa: '.($ficha->programaFormacion->nombre ?? 'N/A'));
        $this->info('📅 Fechas: '.($ficha->fecha_inicio ?? 'N/A').' - '.($ficha->fecha_fin ?? 'N/A'));
        $this->info('👨‍🏫 Instructor: '.($ficha->instructor ? $ficha->instructor->persona->primer_nombre.' '.$ficha->instructor->persona->primer_apellido : 'N/A'));
        $this->info('🏢 Ambiente: '.($ficha->ambiente->nombre_ambiente ?? 'N/A'));
        $this->newLine();
    }

    protected function mostrarResultadoValidacion($resultado): void
    {
        if ($resultado['valido']) {
            $this->info('✅ La ficha es VÁLIDA');
        } else {
            $this->error('❌ La ficha es INVÁLIDA');
        }

        if (count($resultado['errores']) > 0) {
            $this->error('🚨 Errores encontrados:');
            foreach ($resultado['errores'] as $error) {
                $this->error("   - {$error}");
            }
        }

        if (count($resultado['advertencias']) > 0) {
            $this->warn('⚠️ Advertencias:');
            foreach ($resultado['advertencias'] as $advertencia) {
                $this->warn("   - {$advertencia}");
            }
        }

        $this->newLine();
    }
}
