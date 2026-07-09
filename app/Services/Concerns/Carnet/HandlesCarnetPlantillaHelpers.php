<?php

namespace App\Services\Concerns\Carnet;

trait HandlesCarnetPlantillaHelpers
{
    protected function crearPlantillaCarnet(string $tipo)
    {
        $width = 600;
        $height = 400;

        $image = imagecreatetruecolor($width, $height);

        $background = $tipo === 'aprendiz' ? imagecolorallocate($image, 52, 152, 219) : imagecolorallocate($image, 41, 128, 185);

        imagefilledrectangle($image, 0, 0, $width, $height, $background);

        return $image;
    }

    protected function agregarDatosCarnet($image, array $datos): void {}

    protected function agregarQRCarnet($image, string $qrCode): void {}
}
