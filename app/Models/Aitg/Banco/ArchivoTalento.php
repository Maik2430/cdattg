<?php

namespace App\Models\Aitg\Banco;

use App\Models\Aitg\PuntoAdicional;
use App\Models\Competencia;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ArchivoTalento extends Model
{
    protected $table = 'aitg_archivos_talento';

    protected $fillable = [
        'user_id',
        'tipo_archivo_id',
        'competencia_id',
        'plan_contratacion_id',
        'perfil_plan_id',
        'punto_adicional_id',
        'storage_disk',
        'storage_path',
        'nombre_original',
        'nombre_almacenado',
        'mime_type',
        'tamano_bytes',
        'estado',
        'user_create_id',
        'user_update_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tipoArchivo(): BelongsTo
    {
        return $this->belongsTo(TipoArchivo::class, 'tipo_archivo_id');
    }

    public function competencia(): BelongsTo
    {
        return $this->belongsTo(Competencia::class);
    }

    public function postulaciones(): HasMany
    {
        return $this->hasMany(PostulacionArchivo::class, 'archivo_talento_id');
    }

    public function urlDescarga(): ?string
    {
        if (! $this->storage_path) {
            return null;
        }

        $disk = Storage::disk($this->storage_disk);

        return $disk->exists($this->storage_path) ? $disk->url($this->storage_path) : null;
    }
}
