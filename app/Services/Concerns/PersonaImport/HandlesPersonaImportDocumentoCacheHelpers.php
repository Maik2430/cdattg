<?php

namespace App\Services\Concerns\PersonaImport;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

trait HandlesPersonaImportDocumentoCacheHelpers
{
    private function warmDocumentoCache(): void
    {
        if ($this->documentoCacheInitialized) {
            return;
        }

        if (! Schema::hasTable('parametros')) {
            Log::warning('La tabla "parametros" no existe; los tipos de documento se resolverán como null');
            $this->documentoCache = [];
            $this->documentoCacheInitialized = true;

            return;
        }

        $temaTiposDocumento = $this->temaRepository->obtenerTiposDocumento();

        if (! $temaTiposDocumento || ! $temaTiposDocumento->parametros) {
            Log::warning('No se encontró el tema de tipos de documento (tema_id=2)');
            $this->documentoCache = [];
            $this->documentoCacheInitialized = true;

            return;
        }

        $this->documentoCache = [];
        $nombresNormalizados = [];
        foreach ($temaTiposDocumento->parametros as $parametro) {
            $nameOriginal = $parametro->name;
            $nameNormalized = $this->normalizarTextoSinTildes($nameOriginal);
            $this->documentoCache[$nameNormalized] = (int) $parametro->id;

            $nombresNormalizados[] = [
                'original' => $nameOriginal,
                'normalizado' => $nameNormalized,
                'id' => $parametro->id,
            ];
        }

        Log::info('Cache de tipos de documento inicializado', [
            'normalizaciones' => $nombresNormalizados,
            'cache_keys' => array_keys($this->documentoCache),
            'cache_values' => $this->documentoCache,
        ]);

        if (empty($this->documentoCache)) {
            Log::warning('No se encontraron parámetros en el tema de tipos de documento');
        }

        $this->documentoCacheInitialized = true;
    }

    private function normalizarTextoSinTildes(string $texto): string
    {
        $texto = mb_strtoupper($texto, 'UTF-8');

        $texto = str_replace(
            ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü'],
            ['A', 'E', 'I', 'O', 'U', 'N', 'U'],
            $texto
        );

        return $texto;
    }

    private function resolverTipoDocumentoId(?string $valor): ?int
    {
        if (! $valor) {
            return null;
        }

        $normalizado = $this->normalizarTextoSinTildes(trim($valor));

        $clave = $this->documentAliasMap[$normalizado] ?? $normalizado;

        $tipoDocumentoId = $this->documentoCache[$clave] ?? null;

        if (! $tipoDocumentoId && ! empty($clave)) {
            Log::warning('Tipo de documento no encontrado en cache', [
                'valor_original' => $valor,
                'valor_normalizado' => $normalizado,
                'clave_buscada' => $clave,
                'cache_keys' => array_keys($this->documentoCache),
                'cache_values' => $this->documentoCache,
            ]);
        }

        return $tipoDocumentoId;
    }
}
