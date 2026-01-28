<?php

namespace App\Repositories;

use App\Models\Instructor;
use Illuminate\Support\Facades\Log;

class InstructorRepository
{
    public function getInstructor($personaId)
    {
        Log::info('=== DEBUG INSTRUCTORREPOSITORY GETINSTRUCTOR ===');
        Log::info('Buscando instructor con persona_id: ' . $personaId);
        
        $instructor = Instructor::where('persona_id', $personaId)->first();
        
        Log::info('Instructor encontrado: ' . ($instructor ? 'SI - ID: ' . $instructor->id : 'NO'));
        Log::info('=== FIN DEBUG REPOSITORIO INSTRUCTOR ===');
        
        return $instructor;
    }
}