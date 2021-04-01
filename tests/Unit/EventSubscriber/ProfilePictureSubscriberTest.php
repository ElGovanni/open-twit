<?php

namespace App\Tests\Unit\EventSubscriber;

use App\Entity\User\ProfilePicture;
use App\EventSubscriber\ProfilePictureSubscriber;
use App\Storage\WebLocalAdapter;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;

class ProfilePictureSubscriberTest extends Unit
{
    private EntityManagerInterface $entityManager;

    protected function _before()
    {
        parent::_before();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testPostLoadSetsFileAbsolutePath()
    {
        $profilePicture = new ProfilePicture();
        $profilePicture->setFileName('testFileName');

        $args = new LifecycleEventArgs($profilePicture, $this->entityManager);
        $fileSystem = $this->createMock(Filesystem::class);
        $adapter = $this->createMock(WebLocalAdapter::class);
        $fileSystem->method('getAdapter')->willReturn($adapter);

        $adapter->expects($this->once())->method('getUrl');

        $logger = $this->createMock(LoggerInterface::class);

        $subscriber = new ProfilePictureSubscriber($fileSystem, $logger);

        $subscriber->postLoad($args);
    }

    public function testPreRemoveRemovesOldFile()
    {
        $profilePicture = new ProfilePicture();
        $profilePicture->setFileName('testFileName');

        $args = new LifecycleEventArgs($profilePicture, $this->entityManager);
        $fileSystem = $this->createMock(Filesystem::class);
        $fileSystem->expects($this->once())->method('delete');

        $logger = $this->createMock(LoggerInterface::class);

        $subscriber = new ProfilePictureSubscriber($fileSystem, $logger);

        $subscriber->preRemove($args);
    }
}
