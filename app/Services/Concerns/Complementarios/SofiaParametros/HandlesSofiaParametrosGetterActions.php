<?php

namespace App\Services\Concerns\Complementarios\SofiaParametros;

trait HandlesSofiaParametrosGetterActions
{
    public static function getNoRegistradoId(): ?int
    {
        return self::getParametroId('NO REGISTRADO');
    }

    public static function getRegistradoId(): ?int
    {
        return self::getParametroId('REGISTRADO');
    }

    public static function getRequiereCambioId(): ?int
    {
        return self::getParametroId('REQUIERE CAMBIO');
    }

    public static function getValidarId(): ?int
    {
        return self::getParametroId('VALIDAR');
    }

    public static function getExitosoId(): ?int
    {
        return self::getParametroId('EXITOSO');
    }

    public static function getErrorId(): ?int
    {
        return self::getParametroId('ERROR');
    }

    public static function getAdvertenciaId(): ?int
    {
        return self::getParametroId('ADVERTENCIA');
    }

    public static function getPendingId(): ?int
    {
        return self::getParametroId('PENDING');
    }

    public static function getProcessingId(): ?int
    {
        return self::getParametroId('PROCESSING');
    }

    public static function getCompletedId(): ?int
    {
        return self::getParametroId('COMPLETED');
    }

    public static function getFailedId(): ?int
    {
        return self::getParametroId('FAILED');
    }
}
