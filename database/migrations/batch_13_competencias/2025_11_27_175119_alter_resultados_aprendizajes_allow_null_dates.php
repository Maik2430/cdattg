<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('resultados_aprendizajes')) {
            return;
        }

        // Las fechas se eliminaron en una migración previa; volver a crearlas como nullable.
        if (! Schema::hasColumn('resultados_aprendizajes', 'fecha_inicio')) {
            Schema::table('resultados_aprendizajes', function (Blueprint $table) {
                $table->date('fecha_inicio')->nullable()->after('duracion');
                $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            });

            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('resultados_aprendizajes', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->change();
            $table->date('fecha_fin')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('resultados_aprendizajes')) {
            return;
        }

        if (! Schema::hasColumn('resultados_aprendizajes', 'fecha_inicio')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('resultados_aprendizajes', function (Blueprint $table) {
                $table->dropColumn(['fecha_inicio', 'fecha_fin']);
            });

            return;
        }

        Schema::table('resultados_aprendizajes', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable(false)->change();
            $table->date('fecha_fin')->nullable(false)->change();
        });
    }
};
