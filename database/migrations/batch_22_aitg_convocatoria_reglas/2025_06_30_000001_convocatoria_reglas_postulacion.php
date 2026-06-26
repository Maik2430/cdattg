<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('aitg_convocatorias') && ! Schema::hasColumn('aitg_convocatorias', 'postulacion_seleccionada_id')) {
            Schema::table('aitg_convocatorias', function (Blueprint $table) {
                $table->foreignId('postulacion_seleccionada_id')
                    ->nullable()
                    ->after('estado')
                    ->constrained('aitg_postulaciones_plan')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('aitg_convocatorias') && Schema::hasColumn('aitg_convocatorias', 'postulacion_seleccionada_id')) {
            Schema::table('aitg_convocatorias', function (Blueprint $table) {
                $table->dropConstrainedForeignId('postulacion_seleccionada_id');
            });
        }
    }
};
