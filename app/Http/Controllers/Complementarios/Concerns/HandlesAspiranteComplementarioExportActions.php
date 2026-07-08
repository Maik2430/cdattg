<?php

namespace App\Http\Controllers\Complementarios\Concerns;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HandlesAspiranteComplementarioExportActions
{
    public function exportarAspirantesExcel(int $complementarioId): StreamedResponse|JsonResponse
    {
        try {
            return $this->exportService->exportarAspirantesExcel($complementarioId);
        } catch (Exception $e) {
            Log::error('Error exportando aspirantes a Excel: '.$e->getMessage(), [
                'complementario_id' => $complementarioId,
                'user_id' => Auth::id(),
                'exception' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar el archivo Excel. Por favor intente nuevamente.',
            ], 500);
        }
    }

    public function descargarCedulas(int $complementarioId)
    {
        try {
            return $this->exportService->descargarCedulas($complementarioId);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
