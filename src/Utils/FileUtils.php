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

    public static function joinPaths(array $paths = []): string
    {
        if ([] === $paths) {
            throw new \InvalidArgumentException('Paths are empty.');
        }

        foreach ($paths as $key => $argument) {
            $paths[$key] = trim((string) $argument);

            if (empty($paths[$key])) {
                unset($paths[$key]);
            }
        }

        return preg_replace('#/+#', '/', implode('/', $paths));
    }

    public static function generateFilename(?string $prefix, ?string $extension): string
    {
        return sprintf('%s-%s%s', $prefix ?: 'image', bin2hex(random_bytes(3)), $extension ? '.' . $extension : '');
    }
}
