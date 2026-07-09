<?php

namespace App\Services\Concerns\Complementarios\CatalogoComplementarioImport;

use Illuminate\Support\Facades\DB;

trait HandlesCatalogoComplementarioImportConversionHelpers
{
    private function toBoolSiNo(mixed $valor): bool
    {
        if ($valor === null) {
            return false;
        }

        $normalizado = mb_strtoupper(trim((string) $valor));

        return $normalizado === 'SI';
    }

    private function toInt(mixed $valor, int $porDefecto = 0): int
    {
        if ($valor === null || $valor === '') {
            return $porDefecto;
        }

        if (is_int($valor)) {
            return $valor;
        }

        if (is_float($valor)) {
            return (int) round($valor);
        }

        if (! is_numeric($valor)) {
            return $porDefecto;
        }

        return (int) $valor;
    }

    private function limpiarTexto(mixed $valor): ?string
    {
        $texto = $this->toNullableString($valor);

        if ($texto === null) {
            return null;
        }

        $texto = str_replace(["\r\n", "\r"], "\n", $texto);
        $texto = str_replace(['_x000D_', "\t"], ' ', $texto);
        $texto = preg_replace('/\s+/', ' ', $texto) ?? $texto;

        return trim($texto) !== '' ? trim($texto) : null;
    }

    private function toNullableString(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $texto = trim((string) $valor);

        return $texto !== '' ? $texto : null;
    }

    /**
     * Convierte el string de modalidad a modalidad_id (ParametroTema)
     */
    private function convertirModalidadAId(mixed $modalidadString): ?int
    {
        if ($modalidadString === null || $modalidadString === '') {
            return null;
        }

        $modalidadNormalizada = mb_strtoupper(trim((string) $modalidadString));

        $mapeoModalidad = [
            'PRESENCIAL' => 18,
            'VIRTUAL' => 19,
            'MIXTA' => 20,
        ];

        $parametroId = $mapeoModalidad[$modalidadNormalizada] ?? null;

        if (! $parametroId) {
            return null;
        }

        $parametroTema = DB::table('parametros_temas')
            ->where('tema_id', 5)
            ->where('parametro_id', $parametroId)
            ->first();

        return $parametroTema?->id;
    }
}
