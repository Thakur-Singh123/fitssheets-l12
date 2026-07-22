<?php

namespace App\LegacyExcel;

class LegacyExcelStyle
{
    public static function hexToArgb(string $color): string
    {
        $hex = ltrim($color, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return 'FF'.strtoupper($hex);
    }
}
