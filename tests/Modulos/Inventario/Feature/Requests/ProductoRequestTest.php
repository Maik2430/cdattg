<?php

declare(strict_types=1);

namespace Tests\Inventario\Feature\Request;

use Tests\TestCase;
use App\Http\Requests\Inventario\ProductoRequest;
use App\Models\Inventario\Producto;
use App\Models\ParametroTema;
use App\Models\Parametro;
use App\Models\Inventario\ContratoConvenio;
use App\Models\Inventario\Proveedor;
use App\Models\Ambiente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationValidator;
use PHPUnit\Framework\Attributes\Test;

class ProductoRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ejecutar seeder mínimo con datos esenciales para tests de inventario
        $this->seed(\Tests\Modulos\Inventario\Feature\Requests\Seeders\InventarioRequestTestSeeder::class);
    }

    #[Test]
    public function valida_campos_requeridos_para_store(): void
    {
        $rules = $this->obtenerRules();
        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $camposRequeridos = [
            'name', 'tipo_producto_id', 'descripcion', 'peso',
            'unidad_medida_id', 'cantidad', 'estado_producto_id',
            'categoria_id', 'marca_id', 'contrato_convenio_id',
            'ambiente_id', 'proveedor_id'
        ];
        $this->assertCamposTienenError($validator, $camposRequeridos);
    }

    #[Test]
    public function valida_unicidad_de_producto_en_store(): void
    {
        Producto::factory()->create(['name' => 'PRODUCTO TEST']);

        $rules = $this->obtenerRules();

        $this->validarYVerificarError(
            ['name' => 'PRODUCTO TEST'],
            $rules,
            'name'
        );
    }

    private const ROUTE_AGREGAR_CARRITO = 'inventario.productos.agregar-carrito';
    private const ID_INEXISTENTE = 99999;
    private const CANTIDAD_INVALIDA = 0;
    private const CANTIDAD_VALIDA = 1;
    private const PESO_INVALIDO = -1;
    private const PESO_VALIDO = 10.5;
    private const PRODUCTO_NUEVO = 'PRODUCTO NUEVO';

    private function obtenerRules(): array
    {
        $request = new ProductoRequest();
        return $request->rules();
    }

    private function obtenerRulesParaAgregarCarrito(): array
    {
        $request = new ProductoRequest();
        $this->configurarRouteResolver($request, self::ROUTE_AGREGAR_CARRITO);
        return $request->rules();
    }

    private function configurarRouteResolver(ProductoRequest $request, string $ruta): void
    {
        $request->setRouteResolver(function () use ($ruta) {
            return new class($ruta) {
                private string $ruta;
                
                public function __construct(string $ruta) {
                    $this->ruta = $ruta;
                }
                
                public function named(...$patterns): bool {
                    return in_array($this->ruta, $patterns);
                }
            };
        });
    }

    private function validarYVerificarError(array $data, array $rules, string $campoEsperado): void
    {
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey($campoEsperado, $validator->errors()->toArray());
    }

    #[Test]
    public function valida_que_producto_exista_en_agregar_carrito(): void
    {
        $this->validarCampoEnAgregarCarrito('producto_id', self::ID_INEXISTENTE, self::CANTIDAD_VALIDA);
    }

    #[Test]
    public function valida_cantidad_minima_en_agregar_carrito(): void
    {
        $producto = Producto::factory()->create();
        $this->validarCampoEnAgregarCarrito('cantidad', self::CANTIDAD_INVALIDA, $producto->id, 'producto_id');
    }

    #[Test]
    public function valida_existencia_de_tipo_producto(): void
    {
        $this->validarCampoConDatosBaseProducto('tipo_producto_id', self::ID_INEXISTENTE);
    }

    #[Test]
    public function valida_peso_minimo(): void
    {
        $this->validarCampoConDatosBaseProducto('peso', self::PESO_INVALIDO);
    }

    #[Test]
    public function valida_cantidad_minima_en_store(): void
    {
        $this->validarCampoConDatosBaseProducto('cantidad', self::CANTIDAD_INVALIDA);
    }

    #[Test]
    public function valida_imagen_formato_y_tamaño(): void
    {
        $this->validarCampoConDatosBaseProducto('imagen', 'archivo.pdf');
    }

    #[Test]
    public function acepta_datos_validos_para_store(): void
    {
        // Crear datos mínimos necesarios directamente sin usar factories complejos
        $tipoProducto = $this->crearParametroTema('TIPO PRODUCTO TEST', 'TIPOS DE PRODUCTO');
        $unidadMedida = $this->crearParametroTema('UNIDAD MEDIDA TEST', 'UNIDADES DE MEDIDA');
        $estadoProducto = $this->crearParametroTema('ESTADO PRODUCTO TEST', 'ESTADOS DE PRODUCTO');
        
        $categoriaParametro = $this->crearParametroConTema('CATEGORIA TEST', 'CATEGORIAS');
        $marcaParametro = $this->crearParametroConTema('MARCA TEST', 'MARCAS');

        $rules = $this->obtenerRules();

        $validator = Validator::make([
            'name' => self::PRODUCTO_NUEVO,
            'tipo_producto_id' => $tipoProducto->id,
            'descripcion' => 'Descripción del producto',
            'peso' => self::PESO_VALIDO,
            'unidad_medida_id' => $unidadMedida->id,
            'cantidad' => 5,
            'estado_producto_id' => $estadoProducto->id,
            'categoria_id' => $categoriaParametro->id,
            'marca_id' => $marcaParametro->id,
            'contrato_convenio_id' => $this->crearContratoConvenio()->id,
            'ambiente_id' => $this->crearAmbiente()->id,
            'proveedor_id' => $this->crearProveedor()->id,
        ], $rules);

        $this->assertFalse($validator->fails());
    }

    /**
     * Create ParametroTema directly without complex factory dependencies.
     */
    private function crearParametroTema(string $nombreParametro, string $nombreTema): ParametroTema
    {
        // Crear tema y parametro directamente
        $tema = \App\Models\Tema::create([
            'name' => $nombreTema,
            'status' => 1,
            'user_create_id' => null,
            'user_edit_id' => null,
        ]);
        
        $parametro = \App\Models\Parametro::create([
            'name' => $nombreParametro,
            'status' => 1,
            'user_create_id' => null,
            'user_edit_id' => null,
        ]);
        
        return ParametroTema::create([
            'parametro_id' => $parametro->id,
            'tema_id' => $tema->id,
            'status' => 1,
            'user_create_id' => null,
            'user_edit_id' => null,
        ]);
    }

    private function crearParametroConTema(string $nombreParametro, string $nombreTema): \App\Models\Parametro
    {
        // Crear tema directamente sin usar factory que requiere User
        // Los campos user_create_id y user_edit_id son nullable según la migración
        $tema = \App\Models\Tema::create([
            'name' => $nombreTema,
            'status' => 1,
            'user_create_id' => null,
            'user_edit_id' => null,
        ]);
        
        $parametro = \App\Models\Parametro::create([
            'name' => $nombreParametro,
            'status' => 1,
            'user_create_id' => null,
            'user_edit_id' => null,
        ]);
        
        \App\Models\ParametroTema::create([
            'parametro_id' => $parametro->id,
            'tema_id' => $tema->id,
            'status' => 1,
            'user_create_id' => null,
            'user_edit_id' => null,
        ]);
        
        return $parametro;
    }

    /**
     * Validate field with base producto data.
     */
    private function validarCampoConDatosBaseProducto(string $campo, mixed $valorInvalido): void
    {
        $rules = $this->obtenerRules();
        $datos = [
            'name' => self::PRODUCTO_NUEVO,
            $campo => $valorInvalido,
        ];
        $this->validarYVerificarError($datos, $rules, $campo);
    }

    /**
     * Validate field in agregar carrito context.
     */
    private function validarCampoEnAgregarCarrito(
        string $campo,
        mixed $valorInvalido,
        mixed $valorValido,
        string $campoValido = 'cantidad'
    ): void {
        $rules = $this->obtenerRulesParaAgregarCarrito();
        $datos = [
            $campoValido => $valorValido,
            $campo => $valorInvalido,
        ];
        $this->validarYVerificarError($datos, $rules, $campo);
    }

    /**
     * Assert that multiple fields have errors.
     */
    private function assertCamposTienenError(ValidationValidator $validator, array $campos): void
    {
        $errores = $validator->errors()->toArray();
        foreach ($campos as $campo) {
            $this->assertArrayHasKey($campo, $errores);
        }
    }

    /**
     * Get Ambiente from seeder data.
     */
    private function crearAmbiente(): Ambiente
    {
        // El seeder ya creó el ambiente, solo obtenerlo
        $ambienteId = DB::table('ambientes')->value('id');
        return Ambiente::findOrFail($ambienteId);
    }

    /**
     * Get user ID from seeder data.
     */
    private function obtenerUserId(): int
    {
        $userId = DB::table('users')->value('id');
        if (!$userId) {
            throw new \RuntimeException('User not found. Ensure InventarioRequestTestSeeder was executed.');
        }
        return $userId;
    }

    /**
     * Create Proveedor with unique name for testing.
     */
    private function crearProveedor(): Proveedor
    {
        $userId = $this->obtenerUserId();
        
        // Generar nombre único para evitar constraint UNIQUE
        $proveedorName = 'PROVEEDOR TEST ' . uniqid();
        $proveedorId = DB::table('proveedores')->insertGetId([
            'name' => $proveedorName,
            'nit' => rand(100000000, 999999999) . '-' . rand(0, 9),
            'email' => 'test' . uniqid() . '@proveedor.com',
            'telefono' => '6012345678',
            'direccion' => 'Dirección test',
            'departamento_id' => null,
            'municipio_id' => null,
            'persona_id' => null,
            'estado_id' => null,
            'user_create_id' => $userId,
            'user_update_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return Proveedor::find($proveedorId);
    }

    /**
     * Create ContratoConvenio with unique name and code for testing.
     */
    private function crearContratoConvenio(): ContratoConvenio
    {
        // Crear proveedor mínimo primero
        $proveedor = $this->crearProveedor();
        
        // Crear estado (ParametroTema) mínimo
        $estado = $this->crearParametroTema('ESTADO TEST', 'ESTADOS');
        
        $userId = $this->obtenerUserId();
        
        // Generar nombre y código únicos para evitar constraint UNIQUE
        $uniqueId = uniqid();
        $contratoId = DB::table('contratos_convenios')->insertGetId([
            'name' => 'CONTRATO TEST ' . $uniqueId,
            'codigo' => 'COD-TEST-' . $uniqueId,
            'proveedor_id' => $proveedor->id,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addYear()->format('Y-m-d'),
            'estado_id' => $estado->id,
            'user_create_id' => $userId,
            'user_update_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return ContratoConvenio::find($contratoId);
    }
}

