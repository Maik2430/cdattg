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
        Schema::table('instructors', function (Blueprint $table) {
            // Agregar columna jornadas como JSON para almacenar múltiples jornadas
            // Esta columna almacena un array de IDs de jornadas (parametros_temas) en las que el instructor puede ejercer
            $table->json('jornadas')
                  ->nullable()
                  ->after('tipo_vinculacion_id')
                  ->comment('Array de IDs de jornadas (parametros_temas) en las que el instructor puede ejercer (relacionadas con tema JORNADAS)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            // Eliminar columna jornadas
            $table->dropColumn('jornadas');
        });
    }
};
