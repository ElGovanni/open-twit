<?php

namespace App\ImageFilter;

use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Symfony\Component\HttpFoundation\File\File;

class BoxResize
{
    public function __construct(private ImagineInterface $imagine)
    {}

    public function applyFilter(File $file, int $width, int $height): string
    {
        $picture = $this->imagine->load($file->getContent());

        $picture->resize(new Box($width, $height));

        return $picture->get('webp');
    }
}
