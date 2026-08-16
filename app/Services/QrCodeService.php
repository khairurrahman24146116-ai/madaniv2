<?php

namespace App\Services;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;

/**
 * Service QR Code — render QR sebagai PNG via GD (tanpa ekstensi Imagick).
 *
 * Awalnya simple-qrcode hendak dipakai, tetapi output PNG-nya memerlukan
 * ekstensi Imagick yang tidak tersedia di environment ini. bacon-qr-code
 * (pure PHP) dipakai untuk menghasilkan matriks QR, lalu matriks digambar
 * ke PNG memakai GD dan dikembalikan sebagai data URI agar mudah ditempel
 * di template PDF dompdf.
 */
class QrCodeService
{
    public function dataUri(string $text, int $size = 72, int $quietZone = 4): string
    {
        $matrix = Encoder::encode($text, ErrorCorrectionLevel::M())->getMatrix();

        $modules = $matrix->getWidth();
        $total = $modules + ($quietZone * 2);
        $modulePixel = max(1, (int) floor($size / $total));
        $renderSize = $modulePixel * $total;
        $offset = (int) floor(($size - $renderSize) / 2);

        $image = imagecreatetruecolor($size, $size);
        imagesavealpha($image, true);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefilledrectangle($image, 0, 0, $size - 1, $size - 1, $white);

        $top = $offset + ($quietZone * $modulePixel);
        for ($y = 0; $y < $modules; $y++) {
            for ($x = 0; $x < $modules; $x++) {
                if ($matrix->get($x, $y) > 0) {
                    imagefilledrectangle(
                        $image,
                        $top + ($x * $modulePixel),
                        $top + ($y * $modulePixel),
                        $top + ($x * $modulePixel) + $modulePixel - 1,
                        $top + ($y * $modulePixel) + $modulePixel - 1,
                        $black
                    );
                }
            }
        }

        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);

        return 'data:image/png;base64,'.base64_encode($png);
    }
}
