<?php

namespace App\Models\Aitg\Postulacion;

use App\Models\Aitg\Banco\PostulacionArchivo;
use App\Models\Aitg\Banco\PostulacionPlan;
use App\Models\Aitg\PuntoAdicional;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostulacionPuntoItem extends Model
{
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'cargado' => 'Documento cargado',
        'pendiente_evaluacion' => 'Pendiente evaluación',
        'evaluado' => 'Evaluado',
        'requiere_subsanacion' => 'Requiere subsanación',
    ];

    protected $table = 'aitg_postulacion_punto_items';

    protected $fillable = [
        'postulacion_id',
        'punto_adicional_id',
        'descripcion',
        'puntaje_adicional',
        'es_opcional',
        'postulacion_archivo_id',
        'estado',
        'cumple',
        'observaciones',
        'orden',
    ];

    protected $casts = [
        'puntaje_adicional' => 'decimal:2',
        'es_opcional' => 'boolean',
        'cumple' => 'boolean',
        'orden' => 'integer',
    ];

    public function postulacion(): BelongsTo
    {
        return $this->belongsTo(PostulacionPlan::class, 'postulacion_id');
    }

    public function puntoAdicional(): BelongsTo
    {
        return $this->belongsTo(PuntoAdicional::class, 'punto_adicional_id');
    }

    public function postulacionArchivo(): BelongsTo
    {
        return $this->belongsTo(PostulacionArchivo::class, 'postulacion_archivo_id');
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function tieneDocumento(): bool
    {
        return $this->postulacion_archivo_id !== null;
    }
}
