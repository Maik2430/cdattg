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
        Schema::table('complementarios_ofertados', function (Blueprint $table) {
            // Agregar campos de auditoría de usuario
            $table->foreignId('user_create_id')
                ->nullable()
                ->after('jornada_id')
                ->constrained('users')
                ->onDelete('set null');
            
            $table->foreignId('user_edit_id')
                ->nullable()
                ->after('user_create_id')
                ->constrained('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complementarios_ofertados', function (Blueprint $table) {
            // Eliminar foreign keys primero
            $table->dropForeign(['user_create_id']);
            $table->dropForeign(['user_edit_id']);
            
            // Eliminar columnas
            $table->dropColumn(['user_create_id', 'user_edit_id']);
        });
    }
};
