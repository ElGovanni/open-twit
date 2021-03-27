<?php

namespace App\Tests\Unit\Security;

use App\Entity\User\User;
use App\Security\VerifyTokenEvent;
use Codeception\Test\Unit;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class VerifyTokenEventTest extends Unit
{
    public function testValidateInvalidCode()
    {
        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->method('atPath')->willReturn($builder);

        $executionContext = $this->createMock(ExecutionContextInterface::class);
        $executionContext->expects($this->once())->method('buildViolation')->willReturn($builder);


        $code = '123456';
        $user = new User();
        $user->setUsername('Test');
        $user->setConfirmationToken($code);

        $event = new VerifyTokenEvent($user, '123');
        $event->validate($executionContext);
    }

    public function testValidateValidExpiredCode()
    {
        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->method('atPath')->willReturn($builder);

        $executionContext = $this->createMock(ExecutionContextInterface::class);
        $executionContext->expects($this->once())->method('buildViolation')->willReturn($builder);

        $code = '123456';
        $user = new User();
        $user->setUsername('Test');
        $user->setConfirmationToken($code);

        $event = new VerifyTokenEvent($user, $code);
        $event->validate($executionContext);
    }

    public function testValidateExpiredCode()
    {
        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->method('atPath')->willReturn($builder);

        $executionContext = $this->createMock(ExecutionContextInterface::class);
        $executionContext->expects($this->once())->method('buildViolation')->willReturn($builder);


        $code = '123456';
        $expiredAt = new \DateTime('-10 year');
        $user = new User();
        $user->setUsername('Test');
        $user->setConfirmationToken($code);
        $user->setConfirmationTokenExpireAt($expiredAt);

        $event = new VerifyTokenEvent($user, $code);
        $event->validate($executionContext);
    }

    public function testValidateValidCode()
    {
        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->method('atPath')->willReturn($builder);

        $executionContext = $this->createMock(ExecutionContextInterface::class);
        $executionContext->expects($this->never())->method('buildViolation')->willReturn($builder);


        $code = '123456';
        $expiredAt = new \DateTime('+10 year');
        $user = new User();
        $user->setUsername('Test');
        $user->setConfirmationToken($code);
        $user->setConfirmationTokenExpireAt($expiredAt);

        $event = new VerifyTokenEvent($user, $code);
        $event->validate($executionContext);
    }
}
