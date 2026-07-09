<?php

namespace App\Services\Concerns\Complementarios\SofiaHttp;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

trait HandlesSofiaHttpLoggingHelpers
{
    /**
     * Registrar error de conexión
     */
    private function logConnectionError(ConnectionException $e, string $url, string $cedula): void
    {
        Log::error('Error de conexion con servicio Playwright', [
            'cedula' => $cedula,
            'message' => $e->getMessage(),
            'url' => $url,
            'exception_type' => get_class($e),
        ]);
    }

    /**
     * Registrar error de petición
     */
    private function logRequestError(RequestException $e, string $url, string $cedula): void
    {
        Log::error('Error en la peticion HTTP al servicio Playwright', [
            'cedula' => $cedula,
            'message' => $e->getMessage(),
            'url' => $url,
            'exception_type' => get_class($e),
        ]);
    }

    /**
     * Registrar error de validación
     */
    private function logValidationError(Exception $e, string $url, string $cedula): void
    {
        Log::error('Error al validar cedula con servicio Playwright', [
            'cedula' => $cedula,
            'message' => $e->getMessage(),
            'url' => $url,
            'exception' => get_class($e),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
