<?php

namespace App\Tests\Unit\EventSubscriber;

use App\Entity\User\User;
use App\EventSubscriber\UserSubscriber;
use App\ValueObject\Role;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserSubscriberTest extends Unit
{
    private const encodedPassword = '$argon2id$v=19$m=65536,t=4,p=1$Oth4Xnm8YQ7IIDA0+nA/jw$AeYqnpLVRa0gbe0xfU1CAjnPxFLIkO/po0HbSByGajc';

    private UserPasswordEncoderInterface $passwordEncoder;

    private EntityManagerInterface $entityManager;

    public function testPrePersistWillEncodeUserPlainPassword()
    {
        $user = new User();
        $user->setPlainPassword('123456');

        $preUpdateArgs = new LifecycleEventArgs($user, $this->entityManager);
        $subscriber = new UserSubscriber(false, $this->passwordEncoder);

        $subscriber->prePersist($preUpdateArgs);

        /** @var User $updatedUser */
        $updatedUser = $preUpdateArgs->getEntity();

        $this->assertSame(self::encodedPassword, $updatedUser->getPassword());
    }

    public function testNewUserIsInactive()
    {
        $user = new User();

        $preUpdateArgs = new LifecycleEventArgs($user, $this->entityManager);
        $subscriber = new UserSubscriber(true, $this->passwordEncoder);

        $subscriber->prePersist($preUpdateArgs);

        /** @var User $updatedUser */
        $updatedUser = $preUpdateArgs->getEntity();

        $this->assertContains(Role::INACTIVE, $updatedUser->getRoles());
    }

    protected function _before()
    {
        parent::_before();
        $passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);

        $passwordEncoder->method('encodePassword')->willReturn(self::encodedPassword);

        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }
}
