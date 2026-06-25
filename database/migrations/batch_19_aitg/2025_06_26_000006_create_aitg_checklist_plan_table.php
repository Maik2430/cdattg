<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aitg_checklist_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_contratacion_id')
                ->constrained('aitg_planes_contratacion')
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('consecutivo')->default(1);
            $table->text('descripcion_criterio');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aitg_checklist_plan');
    }
};
