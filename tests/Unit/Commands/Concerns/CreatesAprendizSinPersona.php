<?php

namespace Tests\Unit\Commands\Concerns;

use App\Models\Aprendiz;
use Illuminate\Support\Facades\DB;

trait CreatesAprendizSinPersona
{
    /**
     * Simula un aprendiz huérfano (persona_id sin fila en personas).
     * Usa PRAGMA defer_foreign_keys: compatible con RefreshDatabase (transacciones SQLite),
     * donde foreign_keys=OFF no tiene efecto.
     */
    protected function crearAprendizSinPersona(): Aprendiz
    {
        $aprendiz = Aprendiz::factory()->create();
        $personaIdInexistente = 999_999_999;

        DB::statement('PRAGMA defer_foreign_keys = ON');

        DB::table('aprendices')->where('id', $aprendiz->id)->update([
            'persona_id' => $personaIdInexistente,
        ]);

        $orphan = Aprendiz::query()->findOrFail($aprendiz->id);
        $orphan->unsetRelation('persona');

        return $orphan;
    }
}
