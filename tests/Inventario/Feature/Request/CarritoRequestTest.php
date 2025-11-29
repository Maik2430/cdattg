<?php

declare(strict_types=1);

namespace Tests\Inventario\Feature\Request;

use Tests\TestCase;
use App\Http\Requests\Inventario\CarritoRequest;
use App\Models\Inventario\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;

class CarritoRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->migrateDatabases();
    }

    #[Test]
    public function valida_cantidad_requerida_para_actualizar(): void
    {
        $request = new CarritoRequest();
        $request->setRouteResolver(function () {
            return (object) ['getName' => 'inventario.carrito.actualizar'];
        });

        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('cantidad', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_cantidad_debe_ser_integer_para_actualizar(): void
    {
        $request = new CarritoRequest();
        $request->setRouteResolver(function () {
            return (object) ['getName' => 'inventario.carrito.actualizar'];
        });

        $rules = $request->rules();

        $validator = Validator::make([
            'cantidad' => 'no es numero',
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('cantidad', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_cantidad_minima_para_actualizar(): void
    {
        $request = new CarritoRequest();
        $request->setRouteResolver(function () {
            return (object) ['getName' => 'inventario.carrito.actualizar'];
        });

        $rules = $request->rules();

        $validator = Validator::make([
            'cantidad' => 0,
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('cantidad', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_items_requerido_para_agregar(): void
    {
        $request = new CarritoRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_items_debe_ser_array(): void
    {
        $request = new CarritoRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'items' => 'no es array',
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_items_debe_tener_producto_id(): void
    {
        $request = new CarritoRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'items' => [
                ['cantidad' => 1],
            ],
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items.0.producto_id', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_producto_id_debe_existir(): void
    {
        $request = new CarritoRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'items' => [
                [
                    'producto_id' => 99999,
                    'cantidad' => 1,
                ],
            ],
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items.0.producto_id', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_items_debe_tener_cantidad(): void
    {
        $producto = Producto::factory()->create();

        $request = new CarritoRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'items' => [
                ['producto_id' => $producto->id],
            ],
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items.0.cantidad', $validator->errors()->toArray());
    }

    #[Test]
    public function valida_cantidad_minima_en_items(): void
    {
        $producto = Producto::factory()->create();

        $request = new CarritoRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'items' => [
                [
                    'producto_id' => $producto->id,
                    'cantidad' => 0,
                ],
            ],
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items.0.cantidad', $validator->errors()->toArray());
    }

    #[Test]
    public function acepta_datos_validos_para_actualizar(): void
    {
        $request = new CarritoRequest();
        $request->setRouteResolver(function () {
            return (object) ['getName' => 'inventario.carrito.actualizar'];
        });

        $rules = $request->rules();

        $validator = Validator::make([
            'cantidad' => 5,
        ], $rules);

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function acepta_datos_validos_para_agregar(): void
    {
        $producto1 = Producto::factory()->create();
        $producto2 = Producto::factory()->create();

        $request = new CarritoRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'items' => [
                [
                    'producto_id' => $producto1->id,
                    'cantidad' => 2,
                ],
                [
                    'producto_id' => $producto2->id,
                    'cantidad' => 3,
                ],
            ],
        ], $rules);

        $this->assertFalse($validator->fails());
    }
}

