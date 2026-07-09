<?php

namespace App\Console\Commands\Concerns\CheckUserPermissions;

trait HandlesCheckUserPermissionsGroupingHelpers
{
    protected function groupPermissions($permissions)
    {
        $groups = [
            'PARÁMETROS Y TEMAS' => collect(),
            'UBICACIÓN' => collect(),
            'INFRAESTRUCTURA' => collect(),
            'INSTRUCTORES' => collect(),
            'FICHAS' => collect(),
            'PERSONAS' => collect(),
            'INVENTARIO' => collect(),
            'APRENDICES' => collect(),
            'PROGRAMAS' => collect(),
            'COMPETENCIAS Y RAP' => collect(),
            'OTROS' => collect(),
        ];

        foreach ($permissions as $permission) {
            $name = strtoupper($permission->name);

            if (str_contains($name, 'PARAMETRO') || str_contains($name, 'TEMA')) {
                $groups['PARÁMETROS Y TEMAS']->push($permission);
            } elseif (str_contains($name, 'REGIONAL') || str_contains($name, 'MUNICIPIO')) {
                $groups['UBICACIÓN']->push($permission);
            } elseif (str_contains($name, 'CENTRO') || str_contains($name, 'SEDE') ||
                      str_contains($name, 'BLOQUE') || str_contains($name, 'PISO') ||
                      str_contains($name, 'AMBIENTE')) {
                $groups['INFRAESTRUCTURA']->push($permission);
            } elseif (str_contains($name, 'INSTRUCTOR') || str_contains($name, 'ESPECIALIDAD')) {
                $groups['INSTRUCTORES']->push($permission);
            } elseif (str_contains($name, 'FICHA')) {
                $groups['FICHAS']->push($permission);
            } elseif (str_contains($name, 'PERSONA')) {
                $groups['PERSONAS']->push($permission);
            } elseif (str_contains($name, 'PRODUCTO') || str_contains($name, 'CATALOGO') ||
                      str_contains($name, 'CARRITO') || str_contains($name, 'CATEGORIA') ||
                      str_contains($name, 'MARCA') || str_contains($name, 'PROVEEDOR') ||
                      str_contains($name, 'CONTRATO') || str_contains($name, 'ORDEN') ||
                      str_contains($name, 'PRESTAMO') || str_contains($name, 'DEVOLUCION') ||
                      str_contains($name, 'ENTRADA') || str_contains($name, 'SALIDA') ||
                      str_contains($name, 'INVENTARIO')) {
                $groups['INVENTARIO']->push($permission);
            } elseif (str_contains($name, 'APRENDIZ')) {
                $groups['APRENDICES']->push($permission);
            } elseif (str_contains($name, 'PROGRAMA') || $name === 'VER PROGRAMAS DE FORMACION' ||
                      $name === 'VER PROGRAMA DE FORMACION' || $name === 'CREAR PROGRAMA DE FORMACION' ||
                      $name === 'EDITAR PROGRAMA DE FORMACION' || $name === 'ELIMINAR PROGRAMA DE FORMACION' ||
                      $name === 'CAMBIAR ESTADO PROGRAMA DE FORMACION') {
                $groups['PROGRAMAS']->push($permission);
            } elseif (str_contains($name, 'COMPETENCIA') || str_contains($name, 'RAP') ||
                      str_contains($name, 'RESULTADO')) {
                $groups['COMPETENCIAS Y RAP']->push($permission);
            } else {
                $groups['OTROS']->push($permission);
            }
        }

        return collect($groups)->filter(function ($group) {
            return $group->isNotEmpty();
        });
    }
}
