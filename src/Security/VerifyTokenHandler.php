<?php

namespace App\Security;

use App\ValueObject\Role;
use DateTime;
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
        $user->setConfirmationTokenExpireAt(new DateTime());
        $user->addRole(Role::ACTIVE);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
