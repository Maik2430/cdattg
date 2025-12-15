<?php

declare(strict_types=1);

namespace App\Inventario\Services\Image;

use App\Inventario\Interfaces\Services\ImageServiceInterface;
use App\Models\Inventario\Producto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageService implements ImageServiceInterface
{
    /**
     * Obtiene la imagen por defecto desde configuración
     *
     * @return string
     */
    private function getDefaultImage(): string
    {
        return config('inventario.imagenes.default', 'img/inventario/producto-default.png');
    }

    /**
     * Obtiene el directorio de imágenes desde configuración
     *
     * @return string
     */
    private function getImageDirectory(): string
    {
        return config('inventario.imagenes.directorio', 'imagenes_productos');
    }

    public function procesarImagen(?UploadedFile $imagen): string
    {
        if (!$imagen || !$imagen->isValid()) {
            return $this->getDefaultImage();
        }

        try {
            $directory = $this->getImageDirectory();
            $nombreArchivo = time() . '_' . uniqid() . '.' . $imagen->getClientOriginalExtension();
            
            $rutaStorage = Storage::disk('public')->putFileAs($directory, $imagen, $nombreArchivo);
            
            return 'storage/' . $rutaStorage;
        } catch (\Exception $e) {
            Log::error('Error al procesar imagen de producto: ' . $e->getMessage());
            return $this->getDefaultImage();
        }
    }

    public function procesarImagenParaActualizacion(
        ?UploadedFile $imagen,
        Producto $producto
    ): string {
        if (!$imagen || !$imagen->isValid()) {
            return $producto->imagen ?? $this->getDefaultImage();
        }

        $this->eliminarImagenSiExiste($producto);

        return $this->procesarImagen($imagen);
    }

    public function eliminarImagenSiExiste(Producto $producto): void
    {
        $defaultImage = $this->getDefaultImage();

        if ($producto->imagen && $producto->imagen !== $defaultImage) {
            // Si la imagen está en formato storage/..., convertir a ruta de storage
            // Ejemplo: storage/imagenes_productos/nombre.jpg -> imagenes_productos/nombre.jpg
            $rutaStorage = str_replace('storage/', '', $producto->imagen);
            
            // Intentar eliminar desde storage usando disco 'public'
            if (Storage::disk('public')->exists($rutaStorage)) {
                Storage::disk('public')->delete($rutaStorage);
            }
            
            // También intentar eliminar desde public_path por compatibilidad (legacy)
            if (file_exists(public_path($producto->imagen))) {
                unlink(public_path($producto->imagen));
            }
        }
    }
}

