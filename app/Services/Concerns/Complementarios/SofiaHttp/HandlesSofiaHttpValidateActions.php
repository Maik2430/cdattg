<?php

namespace App\Services\Concerns\Complementarios\SofiaHttp;

use App\Exceptions\Complementarios\SofiaConnectionException;
use App\Exceptions\Complementarios\SofiaRequestException;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait HandlesSofiaHttpValidateActions
{
    /**
     * Validar una cédula a través del servicio Playwright
     */
    public function validate(string $cedula): string
    {
        $this->checkHealth();

        $validateUrl = $this->baseUrl.'/validate';

        Log::info('Enviando peticion HTTP al servicio Playwright', [
            'url' => $validateUrl,
            'cedula' => $cedula,
        ]);

        try {
            Log::info('Iniciando validacion HTTP', ['cedula' => $cedula]);
            $startTime = microtime(true);

            $response = Http::timeout($this->timeout)
                ->post($validateUrl, [
                    'cedula' => $cedula,
                ]);

            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            Log::info('Respuesta recibida del servicio Playwright', [
                'cedula' => $cedula,
                'status_code' => $response->status(),
                'duration' => $duration,
            ]);

            return $this->parseResponse($response, $cedula, $duration);
        } catch (ConnectionException $e) {
            $this->logConnectionError($e, $validateUrl, $cedula);
            throw new SofiaConnectionException(
                "No se pudo conectar al servicio Playwright en {$validateUrl}: ".$e->getMessage(),
                0,
                $e
            );
        } catch (RequestException $e) {
            $this->logRequestError($e, $validateUrl, $cedula);
            throw new SofiaRequestException(
                'Error en la peticion al servicio Playwright: '.$e->getMessage(),
                0,
                $e
            );
        } catch (Exception $e) {
            $this->logValidationError($e, $validateUrl, $cedula);
            throw $e;
        }
    }

    /**
     * Verificar que el servicio esté disponible
     */
    public function checkHealth(): void
    {
        $healthUrl = $this->baseUrl.'/health';
        try {
            $healthResponse = Http::timeout(self::HEALTH_CHECK_TIMEOUT)->get($healthUrl);
            if (! $healthResponse->successful()) {
                Log::warning('Servicio Playwright no responde al health check', [
                    'health_url' => $healthUrl,
                    'status' => $healthResponse->status(),
                ]);
            } else {
                Log::debug('Servicio Playwright esta disponible (health check OK)');
            }
        } catch (Exception $e) {
            Log::warning('No se pudo verificar health del servicio Playwright', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
