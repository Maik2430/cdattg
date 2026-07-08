<?php

namespace App\Http\Controllers\Concerns\AsistenciaAprendices;

use App\Models\AsistenciaAprendiz;
use App\Models\FichaCaracterizacion;
use App\Models\ParametroTema;
use Carbon\Carbon;
use DateTimeInterface;
use Carbon\Month;
use Carbon\WeekDay;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesAsistenciaAprendicesReadActions
{
    public function index(): Factory|View
    {
        $fichas = FichaCaracterizacion::select('id', 'ficha')->get();

        return view('asistencias.index', compact('fichas'));
    }

    public function getAttendanceByFicha(Request $request)
    {
        try {
            $fichaId = $request->input('ficha');

            if (! $fichaId) {
                return response()->json(['message' => 'ID de ficha no proporcionado'], 400);
            }

            $asistencias = $this->asistenciaService->obtenerPorFicha($fichaId);

            if ($asistencias->isEmpty()) {
                return response()->json(['message' => 'No se encontraron asistencias para la ficha proporcionada'], 404);
            }

            return view('asistencias.asistencia_by_ficha', ['asistencias' => $asistencias]);
        } catch (Exception $e) {
            Log::error('Error obteniendo asistencias por ficha: '.$e->getMessage());

            return response()->json(['message' => 'Error obteniendo asistencias', 'error' => $e->getMessage()], 500);
        }
    }

    public function getAttendanceByDateAndFicha(Request $request)
    {
        $ficha = $request->input('ficha');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        if (! $ficha || ! $fechaInicio || ! $fechaFin) {
            return response()->json(['message' => 'Datos incompletos'], 400);
        }

        try {
            $asistencias = $this->asistenciaService->obtenerPorFichaYFechas($ficha, $fechaInicio, $fechaFin);

            if ($asistencias->isEmpty()) {
                return response()->json(['message' => 'No se encontraron asistencias para la ficha y fechas proporcionadas'], 404);
            }

            return view('asistencias.asistencia_by_date', ['asistencias' => $asistencias]);
        } catch (Exception $e) {
            Log::error('Error obteniendo asistencias por fecha: '.$e->getMessage());

            return response()->json(['message' => 'Error obteniendo asistencias', 'error' => $e->getMessage()], 500);
        }
    }

    public function getDocumentsByFicha(Request $request)
    {
        $fichaId = $request->input('ficha');

        try {
            if (! $fichaId) {
                return response()->json(['message' => 'ID de ficha no proporcionado'], 400);
            }

            $documentos = $this->asistenciaService->obtenerDocumentosPorFicha($fichaId);

            if ($documentos->isEmpty()) {
                return response()->json(['message' => 'No se encontraron documentos para la ficha proporcionada'], 404);
            }

            return view('asistencias.consulta_by_document', ['documentos' => $documentos]);
        } catch (Exception $e) {
            Log::error('Error obteniendo documentos por ficha: '.$e->getMessage());

            return response()->json(['message' => 'Error obteniendo documentos', 'error' => $e->getMessage()], 500);
        }
    }

    public function getAttendanceByDocument(Request $request)
    {
        $document = $request->input('documento');

        if (! $document) {
            return response()->json(['message' => 'Datos incompletos'], 400);
        }

        try {
            $asistencias = $this->asistenciaService->obtenerPorDocumento($document);

            if ($asistencias->isEmpty()) {
                return response()->json(['message' => 'No se encontraron asistencias para el documento proporcionado'], 404);
            }

            return view('asistencias.asistencia_by_document', ['asistencias' => $asistencias]);
        } catch (Exception $e) {
            Log::error('Error obteniendo asistencias por documento: '.$e->getMessage());

            return response()->json(['message' => 'Error obteniendo asistencias', 'error' => $e->getMessage()], 500);
        }
    }

    public function getList(string $ficha, string $jornada)
    {
        $horaEjecucion = Carbon::now()->format('H:i:s');
        $fechaActual = Carbon::now()->format('Y-m-d');

        $obJornada = ParametroTema::whereHas('tema', function ($q): void {
            $q->where('name', 'LIKE', '%JORNADAS%');
        })->whereHas('parametro', function ($query) use ($jornada): void {
            $query->where('name', $jornada);
        })->with('parametro')->first();

        Log::info('Jornada: '.json_encode($obJornada));

        if ($obJornada === null) {
            return response()->json(['message' => 'Jornada no encontrada'], 404);
        }

        $h1Ini = Carbon::parse($obJornada->hora_inicio)->format('H');
        $m1Ini = Carbon::parse($obJornada->hora_inicio)->format('i');
        $h2Ini = Carbon::parse($obJornada->hora_fin)->format('H');
        $m2Fin = Carbon::parse($obJornada->hora_fin)->format('i');

        $asistencias = AsistenciaAprendiz::whereHas('caracterizacion', function ($query) use ($ficha, $jornada): void {
            $query->whereHas('ficha', function ($query) use ($ficha): void {
                $query->where('ficha', $ficha);
            })->whereHas('jornada', function ($query) use ($jornada): void {
                $query->where('jornada', $jornada);
            });
        })->whereDate('created_at', $fechaActual)->get();

        foreach ($asistencias as $asistencia) {
            $hourEnter = Carbon::parse($asistencia->hora_ingreso)->format('H:i:s');
            $dateEnter = Carbon::parse($asistencia->created_at)->format('Y-m-d');

            if ($this->validateHour($horaEjecucion, $h1Ini, $m1Ini, $h2Ini, $m2Fin) && $dateEnter == $fechaActual) {
                return response()->json(['asistencias' => $asistencias], 200);
            }
        }

        return response()->json(['message' => 'No se encontraron asistencias para la ficha y jornada proporcionadas'], 404);
    }

    private function validateHour(DateTimeInterface|WeekDay|Month|string|int|float|null $ingreso, $hora1, $min1, $hora2, $min2): bool
    {
        $horaInicio = Carbon::createFromTime($hora1, $min1, 0);
        $horaFin = Carbon::createFromTime($hora2, $min2, 0);
        $horaIngreso = Carbon::parse($ingreso);

        return $horaIngreso->between($horaInicio, $horaFin);
    }
}
