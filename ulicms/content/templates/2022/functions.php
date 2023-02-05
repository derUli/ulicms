<?php

declare(strict_types=1);

function hexToRgb(string $hex, bool $alpha = false): array {
    $hex = str_replace('#', '', $hex);
    $length = strlen($hex);
    $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
    $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
    $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
    if ($alpha) {
        $rgb['a'] = $alpha;
    }
    return $rgb;
}

function rgbToHex(int $r, int $g, int $b): string {
    return sprintf("#%02x%02x%02x", $r, $g, $b);
}

function getBarColor1(): string {
    $backgroundColor = Settings::get("header-background-color");

    $rgbColor = hexToRgb($backgroundColor);
    $r = $rgbColor['r'];
    $g = $rgbColor['g'];
    $b = $rgbColor['b'];

    $newRGB = [
        'r' => ($r < 128) ? 255 : 0,
        'g' => ($g < 128) ? 255 : 0,
        'b' => ($b < 128) ? 255 : 0
    ];

    return rgbToHex($newRGB['r'], $newRGB['g'], $newRGB['b']);
}

function getHeadlineColor(): string {
    return getBarColor1();
}

function getBarColor2(): string {
    $backgroundColor = Settings::get("header-background-color");

    $rgbColor = hexToRgb($backgroundColor);
    $r = $rgbColor['r'];
    $g = $rgbColor['g'];
    $b = $rgbColor['b'];

    $newRGB = [
        'r' => ($r < 128) ? 200 : 55,
        'g' => ($g < 128) ? 200 : 55,
        'b' => ($b < 128) ? 200 : 55
    ];

    return rgbToHex($newRGB['r'], $newRGB['g'], $newRGB['b']);
}
