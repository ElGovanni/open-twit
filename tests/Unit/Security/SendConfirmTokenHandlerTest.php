<?php

namespace App\Tests\Unit\Security;

use App\Entity\User\User;
use App\Security\ConfirmTokenGenerator\ConfirmTokenGeneratorInterface;
use App\Security\SendConfirmTokenHandler;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;

class SendConfirmTokenHandlerTest extends Unit
{
    public function testInvoke()
    {
        $token = '123456';
        $expireAt = new \DateTime('+6 hours');
        $confirmTokenGenerator = $this->createMock(ConfirmTokenGeneratorInterface::class);
        $confirmTokenGenerator->method('generate')->willReturn($token);
        $confirmTokenGenerator->method('expireAt')->willReturn($expireAt);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('persist');
        $entityManager->method('flush');
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->exactly(1))->method('send');

        $handler = new SendConfirmTokenHandler(
            'noreply@mail.local',
            $mailer,
            $entityManager,
            $confirmTokenGenerator
        );
        $user = new User();
        $user
            ->setUsername('Test')
            ->setEmail('Test@Email.local')
        ;

        $handler($user);

        $this->assertSame($token, $user->getConfirmationToken());
        $this->assertSame($expireAt, $user->getConfirmationTokenExpireAt());
    }
}
