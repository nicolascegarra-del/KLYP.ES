<?php
// One-time script: converts logo_white.png to transparent background
// Delete this file after running it.

$src_path = __DIR__ . '/assets/img/logo_white.png';
$out_path = __DIR__ . '/assets/img/logo_white.png';

if (!function_exists('imagecreatefrompng')) {
    die('GD extension not available.');
}

$src = imagecreatefrompng($src_path);
if (!$src) { die('Could not load source image.'); }

$w = imagesx($src);
$h = imagesy($src);

$dst = imagecreatetruecolor($w, $h);
imagealphablending($dst, false);
imagesavealpha($dst, true);

for ($y = 0; $y < $h; $y++) {
    for ($x = 0; $x < $w; $x++) {
        $c   = imagecolorat($src, $x, $y);
        $r   = ($c >> 16) & 0xFF;
        $g   = ($c >>  8) & 0xFF;
        $b   =  $c        & 0xFF;
        $lum = (int)(0.299 * $r + 0.587 * $g + 0.114 * $b);
        // GD alpha: 0 = opaque, 127 = transparent
        $alpha = 127 - (int)($lum * 127 / 255);
        imagesetpixel($dst, $x, $y, imagecolorallocatealpha($dst, $r, $g, $b, $alpha));
    }
}

imagepng($dst, $out_path, 9);
imagedestroy($src);
imagedestroy($dst);

// Self-delete
unlink(__FILE__);

echo 'Logo converted successfully. This file has been deleted.';
