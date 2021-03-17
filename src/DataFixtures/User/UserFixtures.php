<?php

namespace App\DataFixtures\User;

use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class UserFixtures extends Fixture
{
    public const LOGIN_TEST = '9019e916-13cd-4147-a387-c51dfd69bb8f';

    public const REFRESH_TOKEN_TEST = '356f4409-5ae3-4b1a-a142-7c10df66f095';

    public function load(ObjectManager $manager)
    {
        $userIds = [
            self::LOGIN_TEST,
            self::REFRESH_TOKEN_TEST,
        ];

        for ($i = 0; $i < count($userIds); $i++) {
            $user = new User(Uuid::fromString($userIds[$i]));
            $user->setUsername('User_' . $i);
            $user->setEmail("User${i}@localhost.domain");
            $user->setPlainPassword('123456');
            $manager->persist($user);
        }

        $manager->flush();
    }
}
