<?php

namespace App\Models\Complementarios;

use App\Models\ParametroTema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplementarioCatalogo extends Model
{
    use HasFactory;

    protected $table = 'complementarios_catalogo';

    protected $fillable = [
        'prf_codigo',
        'version',
        'cod_ver',
        'denominacion',
        'nivel_formacion',
        'duracion_horas',
        'requisitos_ingreso',
        'linea_tecnologica',
        'red_tecnologica',
        'red_conocimiento',
        'modalidad_id',
        'apuesta_prioritaria',
        'tipo_permiso',
        'multiple_inscripcion',
        'alamedida',
        'fic',
        'creditos',
        'indice',
        'ocupacion',
        'activo',
    ];

    protected $casts = [
        'multiple_inscripcion' => 'boolean',
        'alamedida' => 'boolean',
        'fic' => 'boolean',
        'activo' => 'boolean',
        'version' => 'integer',
        'duracion_horas' => 'integer',
        'creditos' => 'integer',
    ];

    /**
     * Relación con la modalidad de formación (ParametroTema)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ParametroTema, $this>
     */
    public function modalidad(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'modalidad_id');
    }

    /**
     * Nombre de la modalidad (string) sin sombrear la relación modalidad().
     */
    public function getModalidadNombreAttribute(): ?string
    {
        if (! $this->modalidad_id) {
            return null;
        }

        if ($this->relationLoaded('modalidad')) {
            $relacion = $this->getRelation('modalidad');
        } else {
            $relacion = $this->modalidad()->with('parametro')->first();
        }

        return $relacion?->parametro?->name;
    }
}
