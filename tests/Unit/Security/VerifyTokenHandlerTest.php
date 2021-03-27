<?php

namespace App\Tests\Unit\Security;

use App\Entity\User\User;
use App\Security\VerifyTokenEvent;
use App\Security\VerifyTokenHandler;
use App\ValueObject\Role;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;

class VerifyTokenHandlerTest extends Unit
{
    public function testInvoke()
    {
        $user = new User();
        $user->setUsername('Test');
        $user->setRoles([Role::INACTIVE]);

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->expects($this->exactly(1))->method('persist');
        $entityManager->expects($this->exactly(1))->method('flush');

        $event = new VerifyTokenEvent($user, '123456');
        $handler = new VerifyTokenHandler($entityManager);
        $handler($event);
        $this->assertNotContains(Role::INACTIVE, $user->getRoles());
    }
}
