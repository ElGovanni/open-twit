<?php

namespace App\EventSubscriber;

use App\Entity\User\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserSubscriber implements EventSubscriber
{
    public function __construct(
        private UserPasswordEncoderInterface $passwordEncoder
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (! $entity instanceof User) {
            return;
        }

        $plainPassword = $entity->getPlainPassword();
        if ($plainPassword !== null) {
            $entity->setPassword(
                $this->passwordEncoder->encodePassword($entity, $plainPassword)
            );
        }
    }
}
