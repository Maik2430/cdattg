<?php

namespace App\Models\Aitg\Banco;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidacionDocumento extends Model
{
    protected $table = 'aitg_validaciones_documento';

    protected $fillable = [
        'documento_id',
        'postulacion_archivo_id',
        'validador_user_id',
        'resultado',
        'motivo_rechazo_id',
        'descripcion',
        'fecha_validacion',
    ];

    protected $casts = [
        'fecha_validacion' => 'datetime',
    ];

    public function documento(): BelongsTo
    {
        return $this->belongsTo(DocumentoBanco::class, 'documento_id');
    }

    public function validador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validador_user_id');
    }

    public function postulacionArchivo(): BelongsTo
    {
        return $this->belongsTo(PostulacionArchivo::class, 'postulacion_archivo_id');
    }

    public function motivoRechazo(): BelongsTo
    {
        return $this->belongsTo(MotivoRechazo::class, 'motivo_rechazo_id');
    }
}

