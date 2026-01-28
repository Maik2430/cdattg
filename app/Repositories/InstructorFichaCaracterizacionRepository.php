<?php

namespace App\Repositories;

use App\Models\InstructorFichaCaracterizacion;
use Illuminate\Support\Facades\Log;

class InstructorFichaCaracterizacionRepository
{
    public function getInstructorFichaCaracterizacion($instructorId)
    {
        Log::info('=== DEBUG INSTRUCTORFICHACARACTERIZACIONREPOSITORY ===');
        Log::info('Buscando fichas para instructor_id: ' . $instructorId);
        
        $fichas = InstructorFichaCaracterizacion::where('instructor_id', $instructorId)->get();
        
        Log::info('Cantidad de fichas encontradas: ' . $fichas->count());
        
        if ($fichas->isNotEmpty()) {
            Log::info('Fichas encontradas:');
            foreach ($fichas as $index => $ficha) {
                Log::info("  Ficha {$index}: ID={$ficha->id}, instructor_id={$ficha->instructor_id}, ficha_id={$ficha->ficha_id}");
            }
        } else {
            Log::warning('No se encontraron fichas para el instructor_id: ' . $instructorId);
        }
        
        Log::info('=== FIN DEBUG REPOSITORIO FICHAS ===');
        
        return $fichas;
    }
}
