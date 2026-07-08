<?php

declare(strict_types=1);

namespace App\Models\Concerns\Producto;

use App\Models\Ambiente;
use App\Models\Inventario\ContratoConvenio;
use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Proveedor;
use App\Models\Parametro;
use App\Models\ParametroTema;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasProductoRelations
{
    public function tipoProducto(): BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'tipo_producto_id');
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'unidad_medida_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'estado_producto_id');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'marca_id')
            ->whereHas('temas', function ($query) {
                $query->where('name', 'MARCAS');
            });
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'categoria_id')
            ->whereHas('temas', function ($query) {
                $query->where('name', 'CATEGORIAS');
            });
    }

    public function contratoConvenio(): BelongsTo
    {
        return $this->belongsTo(ContratoConvenio::class);
    }

    public function ambiente(): BelongsTo
    {
        return $this->belongsTo(Ambiente::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function detalleOrdenes(): HasMany
    {
        return $this->hasMany(DetalleOrden::class, 'producto_id');
    }
}
