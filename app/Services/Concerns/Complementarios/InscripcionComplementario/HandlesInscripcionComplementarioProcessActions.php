<?php

namespace App\Services\Concerns\Complementarios\InscripcionComplementario;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesInscripcionComplementarioProcessActions
{
    public function procesarInscripcionGeneral(array $data): RedirectResponse
    {
        try {
            if ($this->personaRepository->existsByDocumentoOrEmail($data['numero_documento'], $data['email'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ya existe una persona registrada con este número de documento o correo electrónico.');
            }

            $this->personaRepository->create($data);

            return redirect()
                ->route('inscripcion.general')
                ->with('success', '¡Registro exitoso! Sus datos han sido guardados correctamente.');

        } catch (Exception $e) {
            Log::error('Error en inscripción general: '.$e->getMessage(), [
                'data' => $data,
                'exception' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al procesar su inscripción. Por favor intente nuevamente.');
        }
    }

    public function procesarInscripcion(array $data, int $programaId): RedirectResponse
    {
        try {
            if ($this->verificarInscripcionExistente($programaId)) {
                return redirect()->back()->with('error', 'Ya estás inscrito en este programa complementario.');
            }

            return DB::transaction(function () use ($data, $programaId) {
                $persona = $this->procesarPersona($data);

                $this->procesarUsuario($data, $persona);

                $aspirante = $this->crearAspirante($persona, $programaId, $data);

                $this->procesarDocumento($data, $aspirante, $persona);

                return redirect()->route('login.index')->with(
                    'success',
                    '¡Inscripción completada exitosamente! Su cuenta de usuario ha sido creada. '.
                    'Puede iniciar sesión con su correo electrónico y número de documento como contraseña.'
                );
            });

        } catch (Exception $e) {
            Log::error('Error en inscripción a programa: '.$e->getMessage(), [
                'programa_id' => $programaId,
                'data' => $data,
                'exception' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al procesar su inscripción. Por favor intente nuevamente.');
        }
    }
}
