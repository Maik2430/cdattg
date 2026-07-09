<?php

namespace App\Http\Controllers\Concerns\Auth;

trait HandlesRegisterFormDataHelpers
{
    private function resolveDocumentos(): object
    {
        $tema = $this->temaRepository->obtenerTiposDocumento();

        if ($tema && $tema->parametros?->count()) {
            return $tema;
        }

        return (object) [
            'parametros' => collect(config('registro.fallback_documentos', [])),
        ];
    }

    private function resolveGeneros(): object
    {
        $tema = $this->temaRepository->obtenerGeneros();

        if ($tema && $tema->parametros?->count()) {
            return $tema;
        }

        return (object) [
            'parametros' => collect(config('registro.fallback_generos', [])),
        ];
    }

    private function resolveCaracterizaciones(): object
    {
        $tema = $this->temaRepository->obtenerCaracterizacionesComplementarias();

        if ($tema && $tema->parametros?->count()) {
            return $tema;
        }

        return (object) ['parametros' => collect()];
    }

    private function resolveVias(): object
    {
        $tema = $this->temaRepository->obtenerVias();

        if ($tema && $tema->parametros?->count()) {
            return $tema;
        }

        return (object) ['parametros' => collect()];
    }

    private function resolveLetras(): object
    {
        $tema = $this->temaRepository->obtenerLetras();

        if ($tema && $tema->parametros?->count()) {
            return $tema;
        }

        return (object) ['parametros' => collect()];
    }

    private function resolveCardinales(): object
    {
        $tema = $this->temaRepository->obtenerCardinales();

        if ($tema && $tema->parametros?->count()) {
            return $tema;
        }

        return (object) ['parametros' => collect()];
    }
}
