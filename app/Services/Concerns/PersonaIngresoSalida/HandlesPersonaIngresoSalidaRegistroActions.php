<?php

namespace App\Services\Concerns\PersonaIngresoSalida;

use App\Exceptions\PersonaException;
use App\Models\PersonaIngresoSalida;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaIngresoSalidaRegistroActions
{
    /**
     * Registra la entrada de una persona
     */
    public function registrarEntrada(
        int $personaId,
        int $sedeId,
        ?int $ambienteId = null,
        ?int $fichaCaracterizacionId = null,
        ?string $observaciones = null,
        ?int $userId = null
    ): PersonaIngresoSalida {
        return DB::transaction(function () use (
            $personaId,
            $sedeId,
            $ambienteId,
            $fichaCaracterizacionId,
            $observaciones,
            $userId
        ) {
            // Verificar si ya tiene un registro abierto (entrada sin salida) en esta sede
            $registroAbierto = PersonaIngresoSalida::where('persona_id', $personaId)
                ->where('sede_id', $sedeId)
                ->whereNull('timestamp_salida')
                ->whereDate('fecha_entrada', Carbon::today())
                ->first();

            if ($registroAbierto) {
                throw new PersonaException('Ya existe un registro de entrada sin salida para hoy en esta sede.');
            }

            // Determinar tipo de persona
            $tipoPersona = $this->determinarTipoPersona($personaId);

            $now = Carbon::now();

            // Crear registro de entrada
            $registro = PersonaIngresoSalida::create([
                'persona_id' => $personaId,
                'sede_id' => $sedeId,
                'tipo_persona' => $tipoPersona,
                'fecha_entrada' => $now->format('Y-m-d'),
                'hora_entrada' => $now->format('H:i:s'),
                'timestamp_entrada' => $now,
                'ambiente_id' => $ambienteId,
                'ficha_caracterizacion_id' => $fichaCaracterizacionId,
                'observaciones' => $observaciones,
                'user_create_id' => $userId ?? Auth::id(),
            ]);

            Log::info('Entrada registrada', [
                'registro_id' => $registro->id,
                'persona_id' => $personaId,
                'sede_id' => $sedeId,
                'tipo_persona' => $tipoPersona,
                'timestamp' => $now->toDateTimeString(),
            ]);

            return $registro;
        });
    }

    /**
     * Registra la salida de una persona
     */
    public function registrarSalida(
        int $personaId,
        int $sedeId,
        ?string $observaciones = null,
        ?int $userId = null
    ): bool {
        return DB::transaction(function () use ($personaId, $sedeId, $observaciones, $userId) {
            // Buscar registro abierto (entrada sin salida) en esta sede
            $registro = PersonaIngresoSalida::where('persona_id', $personaId)
                ->where('sede_id', $sedeId)
                ->whereNull('timestamp_salida')
                ->whereDate('fecha_entrada', Carbon::today())
                ->latest('timestamp_entrada')
                ->first();

            if (! $registro) {
                throw new PersonaException('No se encontró un registro de entrada sin salida para hoy en esta sede.');
            }

            $now = Carbon::now();

            // Actualizar registro con salida
            $registro->update([
                'fecha_salida' => $now->format('Y-m-d'),
                'hora_salida' => $now->format('H:i:s'),
                'timestamp_salida' => $now,
                'observaciones' => $registro->observaciones
                    ? ($registro->observaciones."\nSalida: ".($observaciones ?? ''))
                    : ($observaciones ?? null),
                'user_edit_id' => $userId ?? Auth::id(),
            ]);

            Log::info('Salida registrada', [
                'registro_id' => $registro->id,
                'persona_id' => $personaId,
                'sede_id' => $sedeId,
                'timestamp' => $now->toDateTimeString(),
            ]);

            return true;
        });
    }
}
