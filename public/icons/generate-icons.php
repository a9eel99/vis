<?php
// Run: php generate-icons.php
// Requires: GD extension
// Place your logo as public/icons/icon-512.png first

$source = __DIR__ . '/public/icons/icon-512.png';
$outputDir = __DIR__ . '/public/icons/';

if (!file_exists($source)) {
    // Create a default icon with "VIS" text
    $img = imagecreatetruecolor(512, 512);
    $bg = imagecolorallocate($img, 15, 23, 42); // #0f172a
    $accent = imagecolorallocate($img, 245, 158, 11); // #f59e0b
    $white = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $bg);
    imagefilledroundedrectangle($img, 40, 40, 472, 472, 60, $accent);
    imagestring($img, 5, 200, 240, 'VIS', $bg);
    imagepng($img, $source);
    imagedestroy($img);
    echo "Default icon created at {$source}\n";
}

$sizes = [72, 96, 128, 144, 152, 192, 384, 512];

foreach ($sizes as $size) {
    $src = imagecreatefrompng($source);
    $w = imagesx($src);
    $h = imagesy($src);
    $dst = imagecreatetruecolor($size, $size);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $size, $size, $w, $h);
    $output = $outputDir . "icon-{$size}.png";
    imagepng($dst, $output);
    imagedestroy($src);
    imagedestroy($dst);
    echo "Created: icon-{$size}.png\n";
}

echo "\nDone! All icons generated in public/icons/\n";

function imagefilledroundedrectangle($img, $x1, $y1, $x2, $y2, $r, $color) {
    imagefilledrectangle($img, $x1+$r, $y1, $x2-$r, $y2, $color);
    imagefilledrectangle($img, $x1, $y1+$r, $x2, $y2-$r, $color);
    imagefilledellipse($img, $x1+$r, $y1+$r, $r*2, $r*2, $color);
    imagefilledellipse($img, $x2-$r, $y1+$r, $r*2, $r*2, $color);
    imagefilledellipse($img, $x1+$r, $y2-$r, $r*2, $r*2, $color);
    imagefilledellipse($img, $x2-$r, $y2-$r, $r*2, $r*2, $color);
}