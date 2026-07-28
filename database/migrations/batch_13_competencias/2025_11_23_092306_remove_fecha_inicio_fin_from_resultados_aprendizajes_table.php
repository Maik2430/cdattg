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
        $columns = collect(['fecha_inicio', 'fecha_fin'])
            ->filter(fn (string $column) => Schema::hasColumn('resultados_aprendizajes', $column))
            ->values()
            ->all();

        if ($columns === []) {
            return;
        }

        Schema::table('resultados_aprendizajes', function (Blueprint $table) use ($columns) {
            $table->dropColumn($columns);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resultados_aprendizajes', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->after('duracion');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
        });
    }
};
