<?php

namespace App\Http\Controllers\Concerns\ProgramaFormacion;

use App\Models\Parametro;
use App\Models\ProgramaFormacion;
use App\Models\RedConocimiento;
use Illuminate\Http\Request;

trait HandlesProgramaFormacionBusinessRulesHelpers
{
    /**
     * @return array<string, string>
     */
    protected function validateProgramaFormacionBusinessRules(Request $request, ?ProgramaFormacion $programa = null): array
    {
        $errors = [];

        $codigo = $request->input('codigo');
        $redConocimientoId = $request->input('red_conocimiento_id');

        if ($codigo && $redConocimientoId) {
            $query = ProgramaFormacion::where('codigo', $codigo)
                ->whereHas('redConocimiento', function ($q) use ($redConocimientoId) {
                    $q->where('id', $redConocimientoId);
                });

            if ($programa) {
                $query->where('id', '!=', $programa->id);
            }

            if ($query->exists()) {
                $redConocimiento = RedConocimiento::find($redConocimientoId);
                $errors['codigo'] = "El código '{$codigo}' ya existe para la red de conocimiento '{$redConocimiento->nombre}'.";
            }
        }

        if ($codigo && ! $this->validateProgramaFormacionSenaCodeFormat($codigo)) {
            $errors['codigo'] = 'El código debe tener el formato válido del SENA (6 dígitos numéricos).';
        }

        $nombre = $request->input('nombre');
        if ($nombre && $redConocimientoId) {
            $query = ProgramaFormacion::where('nombre', $nombre)
                ->whereHas('redConocimiento', function ($q) use ($redConocimientoId) {
                    $q->where('id', $redConocimientoId);
                });

            if ($programa) {
                $query->where('id', '!=', $programa->id);
            }

            if ($query->exists()) {
                $redConocimiento = RedConocimiento::find($redConocimientoId);
                $errors['nombre'] = "El nombre '{$nombre}' ya existe para la red de conocimiento '{$redConocimiento->nombre}'.";
            }
        }

        $nivelFormacionId = $request->input('nivel_formacion_id');
        if ($redConocimientoId && $nivelFormacionId) {
            if (! $this->validateProgramaFormacionRedNivelCompatibility((int) $redConocimientoId, (int) $nivelFormacionId)) {
                $errors['nivel_formacion_id'] = 'El nivel de formación seleccionado no es compatible con la red de conocimiento.';
            }
        }

        if ($nombre) {
            $senaValidation = $this->validateProgramaFormacionSenaProgramRules($nombre, (int) $nivelFormacionId);
            if (! empty($senaValidation)) {
                $errors = array_merge($errors, $senaValidation);
            }
        }

        $horasTotales = (int) $request->input('horas_totales');
        $horasLectiva = (int) $request->input('horas_etapa_lectiva');
        $horasProductiva = (int) $request->input('horas_etapa_productiva');

        if ($horasTotales > 0 && ($horasLectiva + $horasProductiva) !== $horasTotales) {
            $errors['horas_totales'] = 'La suma de las horas lectivas y productivas '
                .'debe ser igual al total de horas del programa.';
        }

        return $errors;
    }

    /**
     * @return array<string, string>
     */
    protected function validateProgramaFormacionDeletionRules(ProgramaFormacion $programa): array
    {
        $errors = [];

        $fichasActivas = $programa->fichasCaracterizacion()
            ->where('status', true)
            ->count();

        if ($fichasActivas > 0) {
            $errors['programa'] = "No se puede eliminar el programa '{$programa->nombre}' porque tiene {$fichasActivas} ficha(s) de caracterización activa(s).";
        }

        $aprendicesCount = $programa->fichasCaracterizacion()
            ->join('aprendices', 'fichas_caracterizacion.id', '=', 'aprendices.ficha_caracterizacion_id')
            ->count();

        if ($aprendicesCount > 0) {
            $errors['programa'] = "No se puede eliminar el programa '{$programa->nombre}' porque tiene {$aprendicesCount} aprendiz(es) asociado(s).";
        }

        return $errors;
    }

    protected function validateProgramaFormacionSenaCodeFormat(string $codigo): bool
    {
        return (bool) preg_match('/^\d{6}$/', $codigo);
    }

    protected function validateProgramaFormacionRedNivelCompatibility(int $redConocimientoId, int $nivelFormacionId): bool
    {
        $redConocimiento = RedConocimiento::find($redConocimientoId);
        $nivelFormacion = Parametro::find($nivelFormacionId);

        if (! $redConocimiento || ! $nivelFormacion) {
            return false;
        }

        $compatibilidades = [
            'INFORMÁTICA, DISEÑO Y DESARROLLO DE SOFTWARE' => ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR'],
            'COMERCIO Y VENTAS' => ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'],
            'GESTIÓN ADMINISTRATIVA Y FINANCIERA' => ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR'],
            'HOTELERÍA Y TURISMO' => ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR'],
            'CONSTRUCCIÓN' => ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'],
            'MECÁNICA INDUSTRIAL' => ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'],
        ];

        $nivelesPermitidos = $compatibilidades[$redConocimiento->nombre] ?? ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'];

        return in_array($nivelFormacion->name, $nivelesPermitidos);
    }

    /**
     * @return array<string, string>
     */
    protected function validateProgramaFormacionSenaProgramRules(string $nombre, int $nivelFormacionId): array
    {
        $errors = [];
        $nivelFormacion = Parametro::find($nivelFormacionId);

        if (! $nivelFormacion) {
            return $errors;
        }

        if (strlen($nombre) < 10) {
            $errors['nombre'] = 'El nombre del programa debe tener al menos 10 caracteres.';
        }

        if (preg_match('/[<>{}[\]\\|`~!@#$%^&*()+=]/', $nombre)) {
            $errors['nombre'] = 'El nombre del programa no puede contener caracteres especiales.';
        }

        return $errors;
    }
}
