<?php

namespace App\Services\Concerns\Carnet;

use App\Models\Aprendiz;
use App\Models\Instructor;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log;

trait HandlesCarnetGeneracionActions
{
    private function generarQrPng(string $data): string
    {
        $result = (new Builder(
            writer: new PngWriter,
            data: $data,
            size: 200,
            margin: 1,
        ))->build();

        return $result->getString();
    }

    public function generarCarnetAprendiz(Aprendiz $aprendiz): string
    {
        try {
            $persona = $aprendiz->persona;
            $ficha = $aprendiz->fichaCaracterizacion;

            $qrData = json_encode([
                'tipo' => 'APRENDIZ',
                'id' => $aprendiz->id,
                'documento' => $persona->numero_documento,
                'ficha' => $ficha->ficha ?? 'N/A',
                'generado' => now()->toDateString(),
            ]);

            $qrCode = $this->generarQrPng($qrData);

            $carnet = $this->crearPlantillaCarnet('aprendiz');

            $this->agregarDatosCarnet($carnet, [
                'nombre' => $persona->nombre_completo,
                'documento' => $persona->numero_documento,
                'ficha' => $ficha->ficha ?? 'N/A',
                'programa' => $ficha->programaFormacion->nombre ?? 'N/A',
                'tipo' => 'APRENDIZ',
            ]);

            $this->agregarQRCarnet($carnet, $qrCode);

            $filename = "carnets/aprendices/carnet_{$aprendiz->id}_{$persona->numero_documento}.png";
            $carnet->save(storage_path("app/public/{$filename}"));

            Log::info('Carnet de aprendiz generado', [
                'aprendiz_id' => $aprendiz->id,
                'archivo' => $filename,
            ]);

            return $filename;
        } catch (\Exception $e) {
            Log::error('Error generando carnet de aprendiz', [
                'aprendiz_id' => $aprendiz->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function generarCarnetInstructor(Instructor $instructor): string
    {
        try {
            $persona = $instructor->persona;

            $qrData = json_encode([
                'tipo' => 'INSTRUCTOR',
                'id' => $instructor->id,
                'documento' => $persona->numero_documento,
                'regional' => $instructor->regional->nombre ?? 'N/A',
                'generado' => now()->toDateString(),
            ]);

            $qrCode = $this->generarQrPng($qrData);

            $carnet = $this->crearPlantillaCarnet('instructor');

            $this->agregarDatosCarnet($carnet, [
                'nombre' => $persona->nombre_completo,
                'documento' => $persona->numero_documento,
                'regional' => $instructor->regional->nombre ?? 'N/A',
                'especialidad' => $instructor->especialidades['principal'] ?? 'N/A',
                'tipo' => 'INSTRUCTOR',
            ]);

            $this->agregarQRCarnet($carnet, $qrCode);

            $filename = "carnets/instructores/carnet_{$instructor->id}_{$persona->numero_documento}.png";
            $carnet->save(storage_path("app/public/{$filename}"));

            Log::info('Carnet de instructor generado', [
                'instructor_id' => $instructor->id,
                'archivo' => $filename,
            ]);

            return $filename;
        } catch (\Exception $e) {
            Log::error('Error generando carnet de instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
