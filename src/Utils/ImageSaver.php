<?php

namespace App\Utils;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageSaver
{
    private const MAX_SIZE = 600;

    private $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    public function save(UploadedFile $file, string $dirPublic, string $dirUpload, string $prefix = null): string
    {
        $tempFilename = tempnam('', 'upl') . $file->guessExtension();

        $filename = sprintf('%s-%s.webp', $prefix ? $prefix : 'image', bin2hex(random_bytes(3)));

        $file = $file->move(sys_get_temp_dir(), $tempFilename);

        [$iwidth, $iheight] = getimagesize($file->getPathname());

        $ratio = $iwidth / $iheight;

        $width = self::MAX_SIZE;
        $height = self::MAX_SIZE;

        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $photo = $this->imagine->open($file->getPathname());
        $photo
            ->resize(new Box($width, $height), ImageInterface::FILTER_LANCZOS)
            ->save(sprintf('%s%s/%s', $dirPublic, $dirUpload, $filename));

        return sprintf('%s/%s', $dirUpload, $filename);
    }
}
