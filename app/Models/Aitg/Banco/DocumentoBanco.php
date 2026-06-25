<?php

namespace App\Models\Aitg\Banco;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class DocumentoBanco extends Model
{
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'en_revision' => 'En revisión',
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado',
    ];

    protected $table = 'aitg_documentos_banco';

    protected $fillable = [
        'solicitud_id',
        'tipo_archivo_id',
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

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(SolicitudBanco::class, 'solicitud_id');
    }

    public function tipoArchivo(): BelongsTo
    {
        return $this->belongsTo(TipoArchivo::class, 'tipo_archivo_id');
    }

    public function validaciones(): HasMany
    {
        return $this->hasMany(ValidacionDocumento::class, 'documento_id')->orderByDesc('fecha_validacion');
    }

    public function ultimaValidacion(): ?ValidacionDocumento
    {
        return $this->validaciones()->first();
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
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
