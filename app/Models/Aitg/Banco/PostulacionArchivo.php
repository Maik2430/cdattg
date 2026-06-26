<?php

namespace App\Models\Aitg\Banco;

use App\Models\Aitg\PuntoAdicional;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostulacionArchivo extends Model
{
    protected $table = 'aitg_postulacion_archivos';

    protected $fillable = [
        'postulacion_id',
        'archivo_talento_id',
        'tipo_archivo_id',
        'punto_adicional_id',
        'perfil_plan_id',
        'estado',
    ];

    public function postulacion(): BelongsTo
    {
        return $this->belongsTo(PostulacionPlan::class, 'postulacion_id');
    }

    public function archivoTalento(): BelongsTo
    {
        return $this->belongsTo(ArchivoTalento::class, 'archivo_talento_id');
    }

    public function tipoArchivo(): BelongsTo
    {
        return $this->belongsTo(TipoArchivo::class, 'tipo_archivo_id');
    }

    public function puntoAdicional(): BelongsTo
    {
        return $this->belongsTo(PuntoAdicional::class, 'punto_adicional_id');
    }

    public function perfilPlan(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Aitg\PerfilPlan::class, 'perfil_plan_id');
    }

    public function validaciones(): HasMany
    {
        return $this->hasMany(ValidacionDocumento::class, 'postulacion_archivo_id');
    }
}
