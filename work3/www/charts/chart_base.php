<?php
function add_watermark($img, $text = "DashaK") {
    $width = imagesx($img);
    $height = imagesy($img);

    $color = imagecolorallocatealpha($img, 50, 50, 50, 70);

    $font = 5;

    $scale = 2;
    $tw = imagefontwidth($font) * strlen($text) * $scale;
    $th = imagefontheight($font) * $scale;

    $x = intval(($width - $tw) / 2);
    $y = intval(($height - $th) / 2) - 30;

    for ($i = 0; $i < $scale; $i++) {
        for ($j = 0; $j < $scale; $j++) {
            imagestring($img, $font, $x + $i, $y + $j, $text, $color);
        }
    }
}
