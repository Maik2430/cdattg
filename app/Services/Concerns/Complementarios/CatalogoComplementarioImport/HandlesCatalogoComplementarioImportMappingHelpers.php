<?php

namespace App\Services\Concerns\Complementarios\CatalogoComplementarioImport;

trait HandlesCatalogoComplementarioImportMappingHelpers
{
    /**
     * @param  array<string, mixed>  $encabezados
     * @return array<string, string>
     */
    private function construirMapaColumnas(array $encabezados): array
    {
        $mapa = [];

        foreach ($encabezados as $columna => $titulo) {
            if (! is_string($titulo)) {
                continue;
            }

            $tituloNormalizado = mb_strtoupper(trim($titulo));
            $mapa[$tituloNormalizado] = $columna;
        }

        return $mapa;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, string>  $mapaColumnas
     * @return array<string, mixed>|null
     */
    private function mapearFila(array $row, array $mapaColumnas): ?array
    {
        $nivel = $this->obtenerValor($row, $mapaColumnas, 'NIVEL DE FORMACION');

        if ($nivel === null || mb_strtoupper(trim((string) $nivel)) !== self::NIVEL_CURSO_ESPECIAL) {
            return null;
        }

        $codigo = $this->obtenerValor($row, $mapaColumnas, 'PRF_CODIGO');
        $version = $this->obtenerValor($row, $mapaColumnas, 'PRF_VERSION');
        $codVer = $this->obtenerValor($row, $mapaColumnas, 'COD_VER');
        $denominacion = $this->obtenerValor($row, $mapaColumnas, 'PRF_DENOMINACION');
        $duracion = $this->obtenerValor($row, $mapaColumnas, 'PRF_DURACION_MAXIMA');

        if ($codigo === null || $denominacion === null || $duracion === null) {
            return null;
        }

        $requisitos = $this->obtenerValor($row, $mapaColumnas, 'PRF_DESCRIPCION_REQUISITO');

        return [
            'prf_codigo' => (string) $codigo,
            'version' => $this->toInt($version, 1),
            'cod_ver' => $codVer !== null ? (string) $codVer : null,
            'denominacion' => (string) $denominacion,
            'nivel_formacion' => (string) $nivel,
            'duracion_horas' => $this->toInt($duracion, 0),
            'requisitos_ingreso' => $this->limpiarTexto($requisitos),
            'linea_tecnologica' => $this->toNullableString(
                $this->obtenerValor($row, $mapaColumnas, 'LINEA TECNOLÓGICA')
                    ?? $this->obtenerValor($row, $mapaColumnas, 'LINEA TECNOLÓGICA ')
                    ?? $this->obtenerValor($row, $mapaColumnas, 'LINEA TECNOLÓGICA')
            ),
            'red_tecnologica' => $this->toNullableString($this->obtenerValor($row, $mapaColumnas, 'RED TECNOLÓGICA')),
            'red_conocimiento' => $this->toNullableString($this->obtenerValor($row, $mapaColumnas, 'RED DE CONOCIMIENTO')),
            'modalidad_id' => $this->convertirModalidadAId($this->obtenerValor($row, $mapaColumnas, 'MODALIDAD')),
            'apuesta_prioritaria' => $this->toNullableString($this->obtenerValor($row, $mapaColumnas, 'APUESTAS PRIORITARIAS')),
            'tipo_permiso' => $this->toNullableString($this->obtenerValor($row, $mapaColumnas, 'TIPO PERMISO')),
            'multiple_inscripcion' => $this->toBoolSiNo(
                $this->obtenerValor($row, $mapaColumnas, 'MULTIPLE INSCRIPCION')
            ),
            'alamedida' => $this->toBoolSiNo(
                $this->obtenerValor($row, $mapaColumnas, 'PRF_ALAMEDIDA')
            ),
            'fic' => $this->toBoolSiNo(
                $this->obtenerValor($row, $mapaColumnas, 'FIC')
            ),
            'creditos' => $this->toInt(
                $this->obtenerValor($row, $mapaColumnas, 'PRF_CREDITOS'),
                0
            ),
            'indice' => $this->toNullableString($this->obtenerValor($row, $mapaColumnas, 'INDICE')),
            'ocupacion' => $this->toNullableString($this->obtenerValor($row, $mapaColumnas, 'OCUPACIÓN')),
        ];
    }

    private function obtenerValor(array $row, array $mapaColumnas, string $titulo): mixed
    {
        $clave = mb_strtoupper(trim($titulo));

        if (! array_key_exists($clave, $mapaColumnas)) {
            return null;
        }

        $columna = $mapaColumnas[$clave];

        return $row[$columna] ?? null;
    }
}
