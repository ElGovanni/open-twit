<?php

namespace App\Storage;

interface UrlAdapterInterface
{
    public function getUrl(string $filePath);
}
