<?php

namespace Database\Seeders\Aitg;

use App\Models\Aitg\Banco\MotivoRechazo;
use App\Models\Aitg\Banco\TipoArchivo;
use Illuminate\Database\Seeder;

/** Catálogo inicial del Banco de Instructores AITG. */
class AitgBancoInstructoresSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['HOJA_VIDA', 'Hoja de vida', 'Currículum vitae actualizado del aspirante a instructor.', ['pdf'], true, 1],
            ['DNI', 'Documento Nacional de Identidad (DNI)', 'Copia del documento de identidad vigente.', ['pdf', 'jpg', 'jpeg', 'png'], true, 2],
        ];

        foreach ($tipos as [$codigo, $nombre, $desc, $exts, $obligatorio, $orden]) {
            TipoArchivo::firstOrCreate(
                ['codigo' => $codigo],
                [
                    'nombre' => $nombre,
                    'descripcion' => $desc,
                    'extensiones_permitidas' => $exts,
                    'tamano_max_kb' => 5120,
                    'es_obligatorio' => $obligatorio,
                    'orden' => $orden,
                    'activo' => true,
                ]
            );
        }

        $motivos = [
            ['DOC_ILEGIBLE', 'Documento ilegible o de baja calidad', 1],
            ['DOC_VENCIDO', 'Documento vencido o no vigente', 2],
            ['DOC_INCOMPLETO', 'Documento incompleto', 3],
            ['NO_CORRESPONDE', 'No corresponde al tipo solicitado', 4],
            ['DATOS_NO_COINCIDEN', 'Los datos no coinciden con el registro', 5],
            ['OTRO', 'Otro motivo (ver descripción)', 99],
        ];

        foreach ($motivos as [$codigo, $nombre, $orden]) {
            MotivoRechazo::firstOrCreate(
                ['codigo' => $codigo],
                ['nombre' => $nombre, 'activo' => true, 'orden' => $orden]
            );
        }

        $this->command?->info('✓ Catálogo Banco de Instructores AITG listo.');
    }
}
