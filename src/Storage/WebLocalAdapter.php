<?php

namespace App\Storage;

use League\Flysystem\Adapter\Local;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WebLocalAdapter extends Local implements UrlAdapterInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): void
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getUrl(string $filePath): string
    {
        return $this->urlGenerator->generate('app_assets_readasset', ['path' => $filePath], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
