<?php

namespace App\Models\Complementarios;

use App\Models\Concerns\Complementarios\BuildsComplementarioOfertadoCatalogAttributes;
use App\Models\Concerns\Complementarios\BuildsComplementarioOfertadoDisplayAttributes;
use App\Models\Concerns\Complementarios\BuildsComplementarioOfertadoEstadoAttributes;
use App\Models\Concerns\Complementarios\HasComplementarioOfertadoRelations;
use Database\Factories\ComplementarioOfertadoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplementarioOfertado extends Model
{
    use BuildsComplementarioOfertadoCatalogAttributes;
    use BuildsComplementarioOfertadoDisplayAttributes;
    use BuildsComplementarioOfertadoEstadoAttributes;
    use HasComplementarioOfertadoRelations;
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ComplementarioOfertadoFactory::new();
    }

    protected $table = 'complementarios_ofertados';

    protected $fillable = [
        'catalogo_id',
        'codigo',
        'justificacion',
        'cupos',
        'estado_id',
        'jornada_id',
        'ambiente_id',
        'ambiente_comentario',
    ];

    /**
     * Valores por defecto para los atributos
     */
    protected $attributes = [
        'estado_id' => 3, // ID de parametros_temas para "Sin Oferta" (parametro_id = 277)
    ];
}
