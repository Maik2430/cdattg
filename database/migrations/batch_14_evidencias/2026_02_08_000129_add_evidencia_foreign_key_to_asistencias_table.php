<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('asistencias') || ! Schema::hasTable('evidencias')) {
            return;
        }

        if (! Schema::hasColumn('asistencias', 'evidencia_id')) {
            return;
        }

        $foreignKeys = DB::select(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [DB::getDatabaseName(), 'asistencias', 'evidencia_id']
        );

        if (count($foreignKeys) > 0) {
            return;
        }

        Schema::table('asistencias', function (Blueprint $table) {
            $table->foreign('evidencia_id')
                ->references('id')
                ->on('evidencias')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('asistencias') || ! Schema::hasColumn('asistencias', 'evidencia_id')) {
            return;
        }

        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropForeign(['evidencia_id']);
        });
    }
};
