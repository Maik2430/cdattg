<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventario\Producto;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    private const DEFAULT_IMAGE_PATH = 'img/inventario/producto-default.png';
    private const CHUNK_SIZE = 500;

    public function run(): void
    {
        // Verificar si ya hay productos en la base de datos
        if (Producto::count() > 0) {
            return;
        }

        // Ruta del archivo JSON
        $jsonPath = database_path('seeders/data/productos.json');

        // Cargar JSON
        $productos = json_decode(file_get_contents($jsonPath), true);

        // Normalizar datos mínimos
        foreach ($productos as &$producto) {

            if (empty($producto['imagen'])) {
                $producto['imagen'] = self::DEFAULT_IMAGE_PATH;
            }

            if (isset($producto['codigo_barras'])) {
                $producto['codigo_barras'] = (string)$producto['codigo_barras'];
            }
        }

        // Insertar por chunks
        foreach (array_chunk($productos, self::CHUNK_SIZE) as $chunk) {
            DB::table('productos')->insert($chunk);
        }
    }
}
