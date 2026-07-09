<?php

namespace App\Http\Middleware\Concerns\ValidarFichaCaracterizacion;

use App\Services\FichaCaracterizacionValidationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesValidarFichaCaracterizacionRequestActions
{
    protected FichaCaracterizacionValidationService $validationService;

    public function handle(Request $request, Closure $next, $action = null)
    {
        try {
            if (! $this->debeValidar($request, $action)) {
                return $next($request);
            }

            $userId = null;
            if (auth()->check()) {
                $userId = auth()->id();
            }

            Log::info('Validando ficha de caracterización en middleware', [
                'action' => $action,
                'route' => $request->route()->getName(),
                'user_id' => $userId,
                'timestamp' => now(),
            ]);

            $datos = $this->obtenerDatosFicha($request, $action);

            if (empty($datos)) {
                return $next($request);
            }

            $fichaId = $this->obtenerFichaId($request, $action);

            $resultado = $this->validationService->validarFichaCompleta($datos, $fichaId);

            if (! $resultado['valido']) {
                Log::warning('Validación de ficha fallida en middleware', [
                    'errores' => $resultado['errores'],
                    'action' => $action,
                    'user_id' => $userId,
                ]);

                return $this->manejarErroresValidacion($request, $resultado['errores']);
            }

            if (count($resultado['advertencias']) > 0) {
                session()->flash('advertencias_validacion', $resultado['advertencias']);
            }

            Log::info('Validación de ficha exitosa en middleware', [
                'action' => $action,
                'advertencias' => count($resultado['advertencias']),
                'user_id' => auth()->id(),
            ]);

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Error en middleware de validación de ficha', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'action' => $action,
                'user_id' => auth()->id(),
            ]);

            return $next($request);
        }
    }
}
