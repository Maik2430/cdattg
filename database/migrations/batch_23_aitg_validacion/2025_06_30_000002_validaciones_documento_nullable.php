<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('aitg_validaciones_documento')) {
            return;
        }

        Schema::table('aitg_validaciones_documento', function (Blueprint $table) {
            $table->dropForeign(['documento_id']);
        });

        DB::statement('ALTER TABLE aitg_validaciones_documento MODIFY documento_id BIGINT UNSIGNED NULL');

        Schema::table('aitg_validaciones_documento', function (Blueprint $table) {
            $table->foreign('documento_id')
                ->references('id')
                ->on('aitg_documentos_banco')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('aitg_validaciones_documento')) {
            return;
        }

        Schema::table('aitg_validaciones_documento', function (Blueprint $table) {
            $table->dropForeign(['documento_id']);
        });

        DB::statement('ALTER TABLE aitg_validaciones_documento MODIFY documento_id BIGINT UNSIGNED NOT NULL');

        Schema::table('aitg_validaciones_documento', function (Blueprint $table) {
            $table->foreign('documento_id')
                ->references('id')
                ->on('aitg_documentos_banco')
                ->cascadeOnDelete();
        });
    }
};
