<?php

namespace App\Utils;

class FileUtils
{
    public static function unlinkUpload(string $dirPublic, ?string $imagePath): bool
    {
        if (null === $imagePath) {
            return true;
        }

        return @unlink(sprintf('%s%s', $dirPublic, $imagePath));
    }
}
