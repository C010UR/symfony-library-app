<?php

namespace App\Utils;

class Utils
{
    public const ENCODING = 'UTF-8';

    public static function ucwords(string $string): string
    {
        return mb_convert_case(mb_convert_case($string, MB_CASE_LOWER, self::ENCODING), MB_CASE_TITLE, self::ENCODING);
    }

    public static function getLoremIpsum(int $paragraphs = 5)
    {
        $lorem =
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris placerat dignissim ornare. ' .
            'Sed sodales justo vel scelerisque tempus. Morbi ac posuere sem, sed aliquet arcu. Ut non magna ' .
            'vitae ipsum ultricies pellentesque in vel mi. Integer vel ante nec velit commodo dictum in ' .
            'tempus enim. Nulla gravida posuere vehicula. Pellentesque gravida euismod dolor vitae sollicitudin. ' .
            'In molestie, turpis nec sollicitudin pulvinar, risus diam laoreet ipsum, nec vulputate dolor mi ' .
            'quis odio. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis ' .
            'egestas. Fusce libero nunc, pharetra nec metus vitae, sagittis imperdiet neque.';

        return implode("\n\n", array_fill(0, $paragraphs, $lorem));
    }
}
