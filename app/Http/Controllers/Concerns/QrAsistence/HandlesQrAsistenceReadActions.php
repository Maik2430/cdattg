<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesQrAsistenceReadActions
{
    public function index()
    {
        $user = Auth::user();

        Log::info('=== DEBUG QR_ASISTENCE.CARACTER_SELECTER INDEX ===');
        Log::info('Usuario ID: '.$user->id);
        Log::info('Usuario Persona ID: '.($user->persona_id ?? 'NULL'));
        Log::info('Usuario email: '.$user->email);

        if (! $user->persona_id) {
            Log::warning('El usuario no tiene persona_id asociado');
        }

        $instructorFicha = $this->asistenceQrService->getInstructorFichaIndex($user);
        $diasFormacion = $this->asistenceQrService->getDiasFormacion();

        Log::info('Resultado instructorFicha: '.($instructorFicha ? 'TIENE DATOS' : 'NULL'));
        if ($instructorFicha) {
            Log::info('Cantidad de fichas: '.$instructorFicha->count());
            if ($instructorFicha->isNotEmpty()) {
                foreach ($instructorFicha as $index => $ficha) {
                    Log::info("Ficha {$index}: ID={$ficha->id}, instructor_id={$ficha->instructor_id}, ficha_id={$ficha->ficha_id}");
                }
            }
        }

        Log::info('Dias de formación: '.json_encode($diasFormacion));
        Log::info('=== FIN DEBUG ===');

        if (! $instructorFicha) {
            return view('qr_asistence.caracter_selecter', compact('instructorFicha', 'diasFormacion'))
                ->with('warning', 'No tienes fichas de caracterización asignadas. Contacta al administrador.');
        }

        return view('qr_asistence.caracter_selecter', compact('instructorFicha', 'diasFormacion'));
    }
}
