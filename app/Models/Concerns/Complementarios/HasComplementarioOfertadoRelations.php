<?php

namespace App\Models\Concerns\Complementarios;

use App\Models\Ambiente;
use App\Models\Competencia;
use App\Models\Complementarios\AspiranteComplementario;
use App\Models\Complementarios\ComplementarioCatalogo;
use App\Models\GuiasAprendizaje;
use App\Models\JornadaFormacion;
use App\Models\ParametroTema;
use App\Models\ResultadosAprendizaje;

trait HasComplementarioOfertadoRelations
{
    public function jornada()
    {
        return $this->belongsTo(JornadaFormacion::class, 'jornada_id');
    }

    public function catalogo()
    {
        return $this->belongsTo(ComplementarioCatalogo::class, 'catalogo_id');
    }

    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class, 'ambiente_id');
    }

    /**
     * Relación con el estado parametrizado del programa complementario
     */
    public function estado()
    {
        return $this->belongsTo(ParametroTema::class, 'estado_id');
    }

    public function diasFormacion()
    {
        // dia_id apunta a parametros_temas (Tema: DIAS)
        return $this->belongsToMany(
            ParametroTema::class,
            'complementarios_ofertados_dias_formacion',
            'complementario_id',
            'dia_id'
        )
            ->with('parametro')
            ->withPivot('hora_inicio', 'hora_fin');
    }

    public function aspirantes()
    {
        return $this->hasMany(AspiranteComplementario::class, 'complementario_id');
    }

    /**
     * Relación muchos a muchos con Competencias
     */
    public function competencias()
    {
        return $this->belongsToMany(
            Competencia::class,
            'competencia_complementario',
            'complementario_id',
            'competencia_id'
        )->withTimestamps()
            ->withPivot('user_create_id', 'user_edit_id');
    }

    /**
     * Relación muchos a muchos con Resultados de Aprendizaje
     */
    public function raps()
    {
        return $this->belongsToMany(
            ResultadosAprendizaje::class,
            'resultado_aprendizaje_complementario',
            'complementario_id',
            'rap_id'
        )->withTimestamps()
            ->withPivot('user_create_id', 'user_edit_id');
    }

    /**
     * Relación muchos a muchos con Guías de Aprendizaje
     */
    public function guiasAprendizaje()
    {
        return $this->belongsToMany(
            GuiasAprendizaje::class,
            'guia_aprendizaje_complementario',
            'complementario_id',
            'guia_aprendizaje_id'
        )->withTimestamps()
            ->withPivot('user_create_id', 'user_edit_id');
    }
}
