<?php

namespace App\Console\Commands\Concerns\MigrateModule;

trait DefinesMigrateModuleBatches
{
    /**
     * Módulos disponibles en orden de ejecución
     *
     * @var array<string, string>
     */
    protected array $batches = [
        'batch_01_sistema_base' => 'Sistema Base (users, tokens, jobs)',
        'batch_02_permisos' => 'Permisos y Roles (Spatie)',
        'batch_03_parametros' => 'Parámetros y Configuración',
        'batch_04_ubicaciones' => 'Ubicaciones Geográficas (países, departamentos, municipios, sedes)',
        'batch_05_personas' => 'Personas y Usuarios',
        'batch_06_infraestructura' => 'Infraestructura Física (bloques, pisos, ambientes)',
        'batch_07_programas' => 'Programas de Formación',
        'batch_08_fichas' => 'Fichas de Caracterización',
        'batch_09_instructores_aprendices' => 'Instructores, Aprendices y Vigilantes',
        'batch_10_relaciones' => 'Relaciones (aprendiz-ficha, instructor-ficha, ambiente-ficha)',
        'batch_11_jornadas_horarios' => 'Jornadas, Horarios y Días de Formación',
        'batch_12_asistencias' => 'Asistencias y Registros de Entrada/Salida',
        'batch_13_competencias' => 'Competencias, Resultados de Aprendizaje y Guías',
        'batch_14_evidencias' => 'Evidencias de Aprendizaje',
        'batch_15_logs_auditoria' => 'Logs y Auditoría',
        'batch_16_inventario' => 'Módulo de inventario',
        'batch_17_complementarios' => 'Módulo de Complementarios (cursos complementarios, aspirantes, caracterización)',
        'batch_18_entrada_salida' => 'Módulo de Entradas y Salidas',
    ];
}
