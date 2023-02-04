<?php

namespace App\Helpers;

class ColorsHelper
{
    public static function convertHexToRgb($hex)
    {
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        return $r .',' . $g .',' . $b;
    }

    public static function convertRgbToHex($rgb)
    {
        $rgbUnits = explode(',', $rgb);
        $hex = sprintf(
            "#%02x%02x%02x",
            trim($rgbUnits[0]),
            trim($rgbUnits[1]),
            trim($rgbUnits[2])
        );
        return $hex;
    }
}
