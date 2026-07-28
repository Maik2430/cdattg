<?php

namespace App\Models\Concerns\Complementarios;

use App\Models\Ambiente;
use App\Models\Competencia;
use App\Models\Complementarios\AspiranteComplementario;
use App\Models\Complementarios\ComplementarioCatalogo;
use App\Models\GuiasAprendizaje;
use App\Models\ParametroTema;
use App\Models\ResultadosAprendizaje;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasComplementarioOfertadoRelations
{
    /**
     * @return BelongsTo<ParametroTema, $this>
     */
    public function jornada(): BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'jornada_id');
    }

    /**
     * @return BelongsTo<ComplementarioCatalogo, $this>
     */
    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(ComplementarioCatalogo::class, 'catalogo_id');
    }

    /**
     * @return BelongsTo<Ambiente, $this>
     */
    public function ambiente(): BelongsTo
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
