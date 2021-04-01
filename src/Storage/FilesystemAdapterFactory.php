<?php

namespace App\Storage;

use Google\Cloud\Storage\StorageClient;
use League\Flysystem\AdapterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FilesystemAdapterFactory
{
    public static function createAdapter(
        string $environment,
        UrlGeneratorInterface $urlGenerator,
        ?string $localPath = null,
        ?string $gcloudId = null,
        ?string $gcloudKeyFilePath = null,
        ?string $gcloudBucket = null,
    ): UrlAdapterInterface|AdapterInterface
    {
        if('prod' !== $environment) {
            $adapter = new WebLocalAdapter($localPath);
            $adapter->setUrlGenerator($urlGenerator);
            return $adapter;
        }

        $storage = new StorageClient([
            'projectId' => $gcloudId,
            'keyFilePath' => $gcloudKeyFilePath,
        ]);

        return new GoogleStorageAdapter($storage, $storage->bucket($gcloudBucket));
    }
}
