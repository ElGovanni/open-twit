<?php

namespace App\Security;

use App\ValueObject\Role;
use Doctrine\ORM\EntityManagerInterface;

class VerifyTokenHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public function __invoke(VerifyTokenEvent $command)
    {
        $user = $command->getUser();

        $user->removeRole(Role::INACTIVE);
        $user->setConfirmationToken(null);
        $user->setConfirmationTokenExpireAt(new \DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
