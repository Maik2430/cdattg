<?php

namespace App\Services;

use App\Models\InstructorFichaCaracterizacion;
use App\Repositories\FichaCaracterizacionRepository;
use App\Repositories\ResultadosAprendizajeRepository;
use App\Repositories\CompetenciaRepository;
use App\Repositories\EvidenciasRepository;
use App\Models\Evidencias;
use App\Models\EvidenciaGuiaAprendizaje;
use App\Models\Parametro;

class RegistroActividadesServices
{
    protected $fichaCaracterizacionRepository;
    protected $resultadosAprendizajeRepository;
    protected $competenciaRepository;
    protected $evidenciaRepository;

    public function __construct(FichaCaracterizacionRepository $fichaCaracterizacionRepository,
                                ResultadosAprendizajeRepository $resultadosAprendizajeRepository,
                                CompetenciaRepository $competenciaRepository,
                                EvidenciasRepository $evidenciaRepository)
    {
        $this->fichaCaracterizacionRepository = $fichaCaracterizacionRepository;
        $this->resultadosAprendizajeRepository = $resultadosAprendizajeRepository;
        $this->competenciaRepository = $competenciaRepository;
        $this->evidenciaRepository = $evidenciaRepository;
    }

    public function getActividades(InstructorFichaCaracterizacion $instructorFichaCaracterizacion)
    {
        // Obtener los RAPs asignados directamente desde instructor_ficha_resultados_aprendizaje
        $raps = $instructorFichaCaracterizacion->resultadosAprendizaje;
        
        if ($raps->isEmpty()) {
            throw new \Exception('No hay resultados de aprendizaje asignados a esta ficha de instructor. Contacte al administrador para asignar RAPs.');
        }

        // Obtener todas las guías de aprendizaje de todos los RAPs asignados
        $todasActividades = collect([]);
        
        foreach ($raps as $rap) {
            $guiasAprendizaje = $rap->guiasAprendizaje;
            
            foreach ($guiasAprendizaje as $guiaAprendizaje) {
                $actividades = $guiaAprendizaje->actividades;
                
                foreach ($actividades as $actividad) {
                    $actividad->id_estado = $this->formatearEstadoActividad($actividad);
                    $todasActividades->push($actividad);
                }
            }
        }

        return $todasActividades;
    }

    public function getRaps(InstructorFichaCaracterizacion $instructorFichaCaracterizacion)
    {
        // Obtener la ficha de caracterización asociada
        $fichaCaracterizacion = $this->fichaCaracterizacionRepository->getFichaCaracterizacion($instructorFichaCaracterizacion->ficha_id);

        // Obtener el programa de formación relacionado
        $programaFormacion = $fichaCaracterizacion->programaFormacion;

        // Obtener la competencia actual
        $competenciaActual = $programaFormacion->competenciaActual();
    }

    public function crearEvidencia($data, InstructorFichaCaracterizacion $caracterizacion)
    {
        // Obtener los RAPs asignados directamente desde instructor_ficha_resultados_aprendizaje
        $raps = $caracterizacion->resultadosAprendizaje;
        
        if ($raps->isEmpty()) {
            throw new \Exception('No hay resultados de aprendizaje asignados a esta ficha de instructor. Contacte al administrador para asignar RAPs.');
        }

        // Buscar la primera guía de aprendizaje disponible en los RAPs asignados
        $guiaAprendizaje = null;
        
        foreach ($raps as $rap) {
            $guiaAprendizaje = $rap->guiasAprendizaje->first();
            if ($guiaAprendizaje) {
                break;
            }
        }
        
        if (!$guiaAprendizaje) {
            throw new \Exception('No se encontró ninguna guía de aprendizaje asociada a los RAPs asignados. Contacte al administrador para asignar guías de aprendizaje.');
        }

        $evidenciaId = Evidencias::create($data);

        $dataEvidenciaGuia = [
            'evidencia_id' => $evidenciaId->id,
            'guia_aprendizaje_id' => $guiaAprendizaje->id,
            'user_create_id' => $data['user_create_id'],
            'user_edit_id' => $data['user_edit_id']
        ];

        EvidenciaGuiaAprendizaje::create($dataEvidenciaGuia);
    }

    public function formatearEstadoActividad(Evidencias $evidencia)
    {
        $parametro = Parametro::whereHas('parametrosTemas', function ($query) use ($evidencia) {
            $query->where('parametros_temas.id', $evidencia->id_estado);
        })->first();

        if (!$parametro) {
            throw new \Exception('Parámetro de estado no encontrado para la evidencia ID: ' . $evidencia->id . ' con estado ID: ' . $evidencia->id_estado);
        }

        return $parametro->name;
    }

    public function getGuiasAprendizaje(InstructorFichaCaracterizacion $instructorFichaCaracterizacion)
    {
        // Obtener los RAPs asignados directamente desde instructor_ficha_resultados_aprendizaje
        $raps = $instructorFichaCaracterizacion->resultadosAprendizaje;
        
        if ($raps->isEmpty()) {
            return null;
        }

        // Buscar la primera guía de aprendizaje disponible en los RAPs asignados
        foreach ($raps as $rap) {
            $guiaAprendizaje = $rap->guiasAprendizaje->first();
            if ($guiaAprendizaje) {
                return $guiaAprendizaje;
            }
        }
        
        // Retornar null si no hay guía de aprendizaje, permitiendo que la vista maneje este caso
        return null;
    }
}
