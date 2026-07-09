<?php

declare(strict_types=1);

namespace App\Inventario\Services\Concerns\Orden;

trait HandlesOrdenDescripcionHelpers
{
    /**
     * Genera descripción detallada de la orden
     *
     * @param  mixed  $usuario
     */
    private function generarDescripcionOrden(array $datos, $usuario): string
    {
        $solicitante = $usuario->name ?? 'Usuario';
        $email = $usuario->email ?? '';

        return sprintf(
            "SOLICITUD DE %s\n\n".
            "SOLICITANTE:\n".
            "Nombre: %s\n".
            "Email: %s\n".
            "Rol: %s\n".
            "Programa de Formación: %s\n\n".
            "DETALLES:\n".
            "Tipo: %s\n".
            "%s\n".
            "MOTIVO:\n%s",
            strtoupper($datos['tipo']),
            $solicitante,
            $email,
            $datos['rol'],
            $datos['programa_formacion'],
            ucfirst($datos['tipo']),
            $datos['tipo'] === 'prestamo' && ! empty($datos['fecha_devolucion'])
                ? "Fecha de Devolución: {$datos['fecha_devolucion']}\n"
                : "Sin fecha de devolución\n",
            $datos['descripcion']
        );
    }
}
