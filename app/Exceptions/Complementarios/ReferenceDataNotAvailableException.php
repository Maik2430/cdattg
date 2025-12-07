<?php

declare(strict_types=1);

namespace App\Exceptions\Complementarios;

use RuntimeException;

/**
 * Exception thrown when required reference data (seeders, parameters, etc.)
 * is not available in the database.
 *
 * This exception should be used instead of generic RuntimeException
 * to allow specific handling of missing reference data scenarios.
 */
class ReferenceDataNotAvailableException extends RuntimeException
{
    /**
     * Create a new exception instance with a descriptive message.
     *
     * @param string $message Optional custom message
     * @param string|null $missingData Optional description of what data is missing
     */
    public function __construct(string $message = '', ?string $missingData = null)
    {
        if (empty($message)) {
            $message = 'Datos de referencia no disponibles. Verificar seeders.';
            if ($missingData !== null) {
                $message .= " Datos faltantes: {$missingData}";
            }
        }

        parent::__construct($message);
    }
}

