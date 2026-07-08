<?php

declare(strict_types=1);

namespace App\Models\Inventario;

use App\Models\Concerns\Producto\HasProductoRelations;
use App\Models\Concerns\Producto\ManagesProductoStock;
use App\Models\Concerns\Producto\SyncsProductoLifecycle;
use App\Traits\Seguimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de producto de inventario.
 *
 * Se declara el tipo de la columna 'cantidad' (bigInteger no nulo, casteada a
 * entero en $casts) para que el análisis estático conozca su tipo. Eloquent no
 * expone los tipos de las columnas de forma estática, por lo que sin esta
 * anotación las operaciones aritméticas y los parámetros tipados sobre
 * $producto->cantidad se marcan como incompatibles.
 *
 * @property int $cantidad Cantidad en stock.
 */
class Producto extends Model
{
    use HasFactory, Seguimiento;
    use HasProductoRelations;
    use ManagesProductoStock;
    use SyncsProductoLifecycle;

    protected $table = 'productos';

    protected $fillable = [
        'name',
        'tipo_producto_id',
        'descripcion',
        'peso',
        'unidad_medida_id',
        'cantidad',
        'codigo_barras',
        'estado_producto_id',
        'categoria_id',
        'marca_id',
        'contrato_convenio_id',
        'ambiente_id',
        'proveedor_id',
        'fecha_vencimiento',
        'imagen',
        'user_create_id',
        'user_update_id',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'datetime',
        'cantidad' => 'integer',
    ];
}
