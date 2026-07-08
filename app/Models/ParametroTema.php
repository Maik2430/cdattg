<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Parametro|null $parametro
 * @property-read Tema|null $tema
 */
class ParametroTema extends Model
{
    use HasFactory;

    protected $table = 'parametros_temas';

    protected $fillable = [
        'parametro_id',
        'tema_id',
        'status',
        'user_create_id',
        'user_edit_id',
    ];

    /** @return BelongsTo<Parametro, $this> */
    public function parametro(): BelongsTo
    {
        return $this->belongsTo(Parametro::class);
    }

    /** @return BelongsTo<Tema, $this> */
    public function tema(): BelongsTo
    {
        return $this->belongsTo(Tema::class);
    }
}
