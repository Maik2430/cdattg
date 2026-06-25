<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aitg_perfiles_plan', function (Blueprint $table) {
            $table->text('descripcion_criterio')->nullable()->after('consecutivo');
            $table->text('descripcion_criterio_programa')->nullable()->after('descripcion_criterio');
            $table->boolean('incluye_experiencia')->default(false)->after('descripcion_criterio_programa');
        });

        $this->migrarDatosExistentes();

        Schema::table('aitg_perfiles_plan', function (Blueprint $table) {
            $table->dropForeign(['nivel_formacion_id']);
            $table->dropForeign(['programa_formacion_id']);
            $table->dropColumn(['nivel_formacion_id', 'programa_formacion_id']);
        });
    }

    public function down(): void
    {
        Schema::table('aitg_perfiles_plan', function (Blueprint $table) {
            $table->foreignId('nivel_formacion_id')->nullable()->constrained('parametros')->restrictOnDelete();
            $table->foreignId('programa_formacion_id')->nullable()->constrained('programas_formacion')->restrictOnDelete();
            $table->dropColumn([
                'descripcion_criterio',
                'descripcion_criterio_programa',
                'incluye_experiencia',
            ]);
        });
    }

    private function migrarDatosExistentes(): void
    {
        if (! Schema::hasColumn('aitg_perfiles_plan', 'nivel_formacion_id')) {
            return;
        }

        $filas = DB::table('aitg_perfiles_plan as pp')
            ->leftJoin('parametros as n', 'n.id', '=', 'pp.nivel_formacion_id')
            ->leftJoin('programas_formacion as pf', 'pf.id', '=', 'pp.programa_formacion_id')
            ->select([
                'pp.id',
                'n.name as nivel',
                'pf.nombre as programa',
                'pp.experiencia_relacionada_meses',
                'pp.experiencia_docencia_meses',
            ])
            ->get();

        foreach ($filas as $row) {
            $nivel = trim((string) ($row->nivel ?? ''));
            $programa = trim((string) ($row->programa ?? ''));
            $tieneExp = ((int) $row->experiencia_relacionada_meses) > 0
                || ((int) $row->experiencia_docencia_meses) > 0;

            DB::table('aitg_perfiles_plan')->where('id', $row->id)->update([
                'descripcion_criterio' => $nivel !== '' ? "Nivel de formación: {$nivel}" : 'Criterio de perfil',
                'descripcion_criterio_programa' => $programa !== '' ? "Programa: {$programa}" : null,
                'incluye_experiencia' => $tieneExp,
            ]);
        }
    }
};
