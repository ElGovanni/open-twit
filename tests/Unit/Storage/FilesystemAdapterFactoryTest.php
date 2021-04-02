<?php

namespace App\Tests\Unit\Storage;

use App\Storage\FilesystemAdapterFactory;
use App\Storage\GoogleStorageAdapter;
use Codeception\Test\Unit;
use League\Flysystem\Adapter\Local;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FilesystemAdapterFactoryTest extends Unit
{
    public function testCreateDevAdapter()
    {
        $tmp = sys_get_temp_dir() . '/project-test-upload';
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $adapter = FilesystemAdapterFactory::createAdapter('dev', $urlGenerator, $tmp);

        $this->assertInstanceOf(Local::class, $adapter);
        $this->assertFileExists($tmp);

        rmdir($tmp);
    }

    public function testCreateProdAdapter()
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $adapter = FilesystemAdapterFactory::createAdapter('prod', $urlGenerator, '', 'project-id', __DIR__ . '/../../_data/gcloud-service-key.json', 'project-bucket');

        $this->assertInstanceOf(GoogleStorageAdapter::class, $adapter);
    }
}
