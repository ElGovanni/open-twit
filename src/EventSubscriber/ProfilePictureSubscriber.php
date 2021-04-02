<?php

namespace App\EventSubscriber;

use App\Entity\User\ProfilePicture;
use App\Storage\UrlAdapterInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;

class ProfilePictureSubscriber implements EventSubscriber
{
    public function __construct(
        private FilesystemInterface $filesystem,
        private LoggerInterface $logger,
    )
    {}

    public function getSubscribedEvents(): array
    {
        return [
            Events::postLoad,
            Events::preRemove,
        ];
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (! $entity instanceof ProfilePicture) {
            return;
        }

        /** @var UrlAdapterInterface $adapter */
        $adapter = $this->filesystem->getAdapter();
        $entity->setAbsolutePath($adapter->getUrl($entity->getFileName()));
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (! $entity instanceof ProfilePicture) {
            return;
        }

        try {
            $this->filesystem->delete($entity->getFileName());
        } catch (FileNotFoundException $e) {
            $this->logger->warning(sprintf('Tried to remove file %s which not exists.', $e->getPath()));
        }
    }
}
