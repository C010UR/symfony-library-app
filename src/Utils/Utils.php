<?php

namespace App\Utils;

class Utils
{
    const ENCODING = 'UTF-8';

    public static function ucwords(string $string): string
    {
        return mb_convert_case(mb_convert_case($string, MB_CASE_LOWER, self::ENCODING), MB_CASE_TITLE, self::ENCODING);
    }
}
