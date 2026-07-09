<?php

namespace App\Services\Concerns\Complementarios\SofiaHttp;

use App\Exceptions\Complementarios\SofiaHttpErrorException;
use App\Exceptions\Complementarios\SofiaInvalidResponseException;
use App\Exceptions\Complementarios\SofiaServiceErrorException;
use App\Exceptions\Complementarios\SofiaUnexpectedStatusException;
use Illuminate\Support\Facades\Log;

trait HandlesSofiaHttpResponseHelpers
{
    /**
     * Parsear y validar la respuesta del servicio
     */
    private function parseResponse($response, string $cedula, float $duration): string
    {
        if (! $response->successful()) {
            $this->handleUnsuccessfulResponse($response, $cedula, $duration);
        }

        $responseData = $response->json();

        Log::debug('Respuesta JSON del servicio Playwright', [
            'cedula' => $cedula,
            'response_data' => $responseData,
        ]);

        $this->validateResponseStructure($responseData, $response, $cedula);
        $this->validateResponseStatus($responseData, $cedula, $duration);

        $resultado = $responseData[self::RESPONSE_FIELD_RESULTADO] ?? null;

        if ($resultado === null) {
            Log::error('Respuesta del servicio Playwright sin campo resultado', [
                'cedula' => $cedula,
                'response' => $responseData,
                'raw_body' => $response->body(),
            ]);
            throw new SofiaInvalidResponseException('Respuesta sin resultado del servicio Playwright');
        }

        Log::info('Servicio Playwright completado exitosamente', [
            'cedula' => $cedula,
            'resultado' => $resultado,
            'duration' => $duration,
        ]);

        return $resultado;
    }

    /**
     * Manejar respuesta no exitosa
     */
    private function handleUnsuccessfulResponse($response, string $cedula, float $duration): void
    {
        $statusCode = $response->status();
        $errorBody = $response->body();

        Log::error('Servicio Playwright retorno error HTTP', [
            'cedula' => $cedula,
            'status_code' => $statusCode,
            'response' => $errorBody,
            'duration' => $duration,
        ]);

        throw new SofiaHttpErrorException("Error HTTP {$statusCode} del servicio Playwright: {$errorBody}");
    }

    /**
     * Validar estructura de respuesta
     */
    private function validateResponseStructure(array $responseData, $response, string $cedula): void
    {
        if (! isset($responseData[self::RESPONSE_FIELD_STATUS])) {
            Log::error('Respuesta del servicio Playwright sin campo status', [
                'cedula' => $cedula,
                'response' => $responseData,
                'raw_body' => $response->body(),
            ]);
            throw new SofiaInvalidResponseException('Respuesta invalida del servicio Playwright: falta campo status');
        }
    }

    /**
     * Validar status de respuesta
     */
    private function validateResponseStatus(array $responseData, string $cedula, float $duration): void
    {
        if ($responseData[self::RESPONSE_FIELD_STATUS] === self::STATUS_ERROR) {
            $errorMessage = $responseData[self::RESPONSE_FIELD_MESSAGE] ?? 'Error desconocido del servicio Playwright';
            Log::error('Servicio Playwright reporto error', [
                'cedula' => $cedula,
                'message' => $errorMessage,
                'detail' => $responseData[self::RESPONSE_FIELD_DETAIL] ?? null,
                'duration' => $duration,
            ]);
            throw new SofiaServiceErrorException("Error del servicio Playwright: {$errorMessage}");
        }

        if ($responseData[self::RESPONSE_FIELD_STATUS] !== self::STATUS_OK) {
            Log::error('Respuesta del servicio Playwright con status inesperado', [
                'cedula' => $cedula,
                'status' => $responseData[self::RESPONSE_FIELD_STATUS],
                'response' => $responseData,
            ]);
            throw new SofiaUnexpectedStatusException(
                "Status inesperado del servicio Playwright: {$responseData[self::RESPONSE_FIELD_STATUS]}"
            );
        }
    }
}
