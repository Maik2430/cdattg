<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Aprobacion;

use App\Models\Inventario\DetalleOrden;
use App\Notifications\OrdenAprobadaNotification;
use App\Notifications\OrdenRechazadaNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

trait HandlesAprobacionNotificacionHelpers
{
    /**
     * Notifica la aprobación al solicitante
     */
    private function notificarAprobacion(DetalleOrden $detalleOrden): void
    {
        $solicitante = $detalleOrden->orden->userCreate;
        if (! $solicitante) {
            return;
        }

        Notification::send($solicitante, new OrdenAprobadaNotification($detalleOrden, Auth::user()));
    }

    /**
     * Notifica el rechazo al solicitante
     */
    private function notificarRechazo(DetalleOrden $detalleOrden, string $motivoRechazo): void
    {
        $solicitante = $detalleOrden->orden->userCreate;
        if (! $solicitante) {
            return;
        }

        Notification::send($solicitante, new OrdenRechazadaNotification($detalleOrden, Auth::user(), $motivoRechazo));
    }

    /**
     * Construye descripción al rechazar
     */
    private function construirDescripcionRechazo(string $descripcionAnterior, DetalleOrden $detalleOrden, string $motivoRechazo): string
    {
        $texto = $descripcionAnterior."\n\n--- SOLICITUD RECHAZADA ---\n";
        $texto .= "Producto: {$detalleOrden->producto->name}\n";
        $texto .= "Motivo: {$motivoRechazo}\n";
        $texto .= 'Rechazado por: '.Auth::user()->name."\n";

        return $texto.('Fecha: '.now()->format('d/m/Y H:i')."\n");
    }
}
