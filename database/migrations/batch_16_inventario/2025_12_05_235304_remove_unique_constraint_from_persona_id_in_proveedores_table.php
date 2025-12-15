<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Elimina el constraint unique de persona_id para permitir
     * que una persona sea contacto de múltiples proveedores.
     */
    public function up(): void
    {
        // Eliminar la foreign key si existe
        try {
            Schema::table('proveedores', function (Blueprint $table) {
                $table->dropForeign(['persona_id']);
            });
        } catch (\Exception $e) {
            // La foreign key no existe, continuar
        }

        // Intentar eliminar el índice único si existe
        // Usar try-catch porque el índice puede no existir o tener diferentes nombres
        try {
            Schema::table('proveedores', function (Blueprint $table) {
                $table->dropUnique(['persona_id']);
            });
        } catch (\Exception $e) {
            // El índice no existe o ya fue eliminado, continuar
            // Esto es normal si la migración anterior no creó un índice único
        }

        // Recrear la foreign key sin unique
        Schema::table('proveedores', function (Blueprint $table) {
            try {
                $table->foreign('persona_id')
                    ->references('id')
                    ->on('personas')
                    ->onDelete('set null');
            } catch (\Exception $e) {
                // La foreign key ya existe, continuar
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar la foreign key si existe
        try {
            Schema::table('proveedores', function (Blueprint $table) {
                $table->dropForeign(['persona_id']);
            });
        } catch (\Exception $e) {
            // La foreign key no existe, continuar
        }

        // Crear el índice único si no existe
        // Usar try-catch para evitar errores si ya existe
        try {
            Schema::table('proveedores', function (Blueprint $table) {
                $table->unique('persona_id');
            });
        } catch (\Exception $e) {
            // El índice ya existe, continuar
        }

        // Recrear la foreign key con unique
        Schema::table('proveedores', function (Blueprint $table) {
            try {
                $table->foreign('persona_id')
                    ->references('id')
                    ->on('personas')
                    ->onDelete('set null');
            } catch (\Exception $e) {
                // La foreign key ya existe, continuar
            }
        });
    }

};
