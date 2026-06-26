<?php

namespace Database\Seeders\Aitg;

use App\Models\Aitg\Banco\MotivoRechazo;
use App\Models\Aitg\Banco\TipoArchivo;
use Illuminate\Database\Seeder;

/** Catálogo estructurado del Banco de Talento AITG. */
class AitgBancoInstructoresSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            // [codigo, nombre, desc, categoria, exts, oblig, multiples, regla, fase, orden]
            ['HOJA_VIDA', 'Hoja de vida', 'Hoja de vida actualizada del aspirante.', 'obligatorios_base', ['pdf'], true, false, 'siempre', 'inicial', 1],
            ['DOC_IDENTIDAD', 'Documento de identidad', 'Cédula de ciudadanía por ambas caras en un solo archivo PDF.', 'obligatorios_base', ['pdf'], true, false, 'siempre', 'inicial', 2],
            ['RUT', 'RUT', 'Registro Único Tributario (RUT) actualizado.', 'obligatorios_base', ['pdf'], true, false, 'siempre', 'inicial', 3],
            ['TITULO_ALTERNATIVA', 'Título requerido para esta alternativa', 'Cargue el diploma o acta de grado que soporte el nivel de formación exigido para la alternativa seleccionada.', 'formacion_academica', ['pdf'], true, false, 'requiere_perfil', 'inicial', 10],
            ['EXP_RELACIONADA', 'Certificaciones de experiencia laboral', 'Cargue certificaciones laborales relacionadas con el perfil seleccionado. Cada certificación debe incluir cargo, funciones, fechas y tiempo laborado.', 'experiencia', ['pdf'], true, true, 'requiere_exp_relacionada', 'inicial', 20],
            ['EXP_DOCENTE', 'Certificaciones de experiencia docente', 'Cargue certificaciones que acrediten experiencia docente o de formación, indicando funciones, institución y tiempo laborado.', 'experiencia', ['pdf'], false, true, 'requiere_exp_docente', 'inicial', 21],
            ['ANT_DISCIPLINARIOS', 'Certificado de antecedentes disciplinarios', 'Certificado expedido por la Procuraduría General de la Nación.', 'antecedentes', ['pdf'], true, false, 'siempre', 'inicial', 30],
            ['ANT_FISCALES', 'Certificado de antecedentes fiscales', 'Certificado expedido por la Contraloría General de la República.', 'antecedentes', ['pdf'], true, false, 'siempre', 'inicial', 31],
            ['ANT_JUDICIALES', 'Certificado de antecedentes judiciales', 'Consulta o certificado correspondiente de antecedentes judiciales.', 'antecedentes', ['pdf'], true, false, 'siempre', 'inicial', 32],
            ['CURSO_INTEGRIDAD', 'Curso de integridad', 'Certificado del curso de integridad exigido para el proceso contractual SENA. Se solicita tras ser preseleccionado.', 'requisitos_sena', ['pdf'], true, false, 'siempre', 'post_seleccion', 40],
            ['TARJETA_PROFESIONAL', 'Tarjeta profesional', 'Cargue la tarjeta profesional vigente, cuando aplique al perfil seleccionado.', 'requisitos_sena', ['pdf'], false, false, 'requiere_perfil', 'inicial', 41],
            ['CERT_IDIOMAS', 'Certificado de idiomas', 'Cargue el certificado de idioma si es requisito o soporte adicional del plan.', 'requisitos_sena', ['pdf'], false, false, 'requiere_perfil', 'inicial', 42],
        ];

        foreach ($tipos as [$codigo, $nombre, $desc, $cat, $exts, $obligatorio, $multiples, $regla, $fase, $orden]) {
            TipoArchivo::updateOrCreate(
                ['codigo' => $codigo],
                [
                    'nombre' => $nombre,
                    'descripcion' => $desc,
                    'categoria' => $cat,
                    'extensiones_permitidas' => $exts,
                    'tamano_max_kb' => 5120,
                    'es_obligatorio' => $obligatorio,
                    'permite_multiples' => $multiples,
                    'regla_visibilidad' => $regla,
                    'fase_carga' => $fase,
                    'orden' => $orden,
                    'activo' => true,
                ]
            );
        }

        TipoArchivo::whereIn('codigo', ['DNI', 'HOJA_VIDA'])->update(['activo' => false]);

        $motivos = [
            ['DOC_ILEGIBLE', 'Documento ilegible o de baja calidad', 1],
            ['DOC_VENCIDO', 'Documento vencido o no vigente', 2],
            ['DOC_INCOMPLETO', 'Documento incompleto', 3],
            ['NO_CORRESPONDE', 'No corresponde al tipo solicitado', 4],
            ['DATOS_NO_COINCIDEN', 'Los datos no coinciden con el registro', 5],
            ['PERFIL_NO_SELECCIONADO', 'No seleccionó perfil/alternativa del plan', 6],
            ['OTRO', 'Otro motivo (ver descripción)', 99],
        ];

        foreach ($motivos as [$codigo, $nombre, $orden]) {
            MotivoRechazo::firstOrCreate(
                ['codigo' => $codigo],
                ['nombre' => $nombre, 'activo' => true, 'orden' => $orden]
            );
        }

        $this->command?->info('✓ Catálogo Banco de Talento AITG listo.');
    }
}
