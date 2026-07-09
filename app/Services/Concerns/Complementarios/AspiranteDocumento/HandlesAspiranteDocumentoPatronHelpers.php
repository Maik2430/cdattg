<?php

namespace App\Services\Concerns\Complementarios\AspiranteDocumento;

use App\Models\Persona;
use Illuminate\Support\Facades\Log;

trait HandlesAspiranteDocumentoPatronHelpers
{
    public function construirPatronBusqueda(Persona $persona): string
    {
        $tipoDocumento = $persona->tipoDocumento ? str_replace(
            ' ',
            '_',
            $persona->tipoDocumento->name
        ) : 'DOC';

        return "{$tipoDocumento}_{$persona->numero_documento}_".
            str_replace(' ', '_', $persona->primer_nombre).'_'.
            str_replace(' ', '_', $persona->primer_apellido).'_';
    }

    private function generarVariantesPatron(string $patron): array
    {
        $patrones = [$patron];

        $patrones = array_merge($patrones, $this->crearVarianteConEspacios($patron));
        $patrones = array_merge($patrones, $this->crearVarianteConGuiones($patron));
        $patrones = array_merge($patrones, $this->crearPatronSinNombresVariantes($patron));

        return array_unique($patrones);
    }

    private function crearVarianteConEspacios(string $patron): array
    {
        if (strpos($patron, '_') === false) {
            return [];
        }

        return [str_replace('_', ' ', $patron)];
    }

    private function crearVarianteConGuiones(string $patron): array
    {
        if (strpos($patron, ' ') === false) {
            return [];
        }

        return [str_replace(' ', '_', $patron)];
    }

    private function crearPatronSinNombresVariantes(string $patron): array
    {
        $patronSinNombres = $this->crearPatronSinNombres($patron);
        if ($patronSinNombres === null) {
            return [];
        }

        return [
            $patronSinNombres,
            str_replace('_', ' ', $patronSinNombres),
        ];
    }

    private function crearPatronSinNombres(string $patron): ?string
    {
        $partes = explode('_', $patron);

        if (count($partes) < 4) {
            return null;
        }

        $numeroDocumentoIndex = null;
        for ($i = 0; $i < count($partes); $i++) {
            if (is_numeric($partes[$i])) {
                $numeroDocumentoIndex = $i;
                break;
            }
        }

        if ($numeroDocumentoIndex === null) {
            return null;
        }

        $patronSinNombres = '';
        for ($i = 0; $i <= $numeroDocumentoIndex; $i++) {
            $patronSinNombres .= $partes[$i].'_';
        }

        Log::info('Patrón sin nombres creado', [
            'patron_original' => $patron,
            'patron_sin_nombres' => $patronSinNombres,
        ]);

        return $patronSinNombres;
    }
}
