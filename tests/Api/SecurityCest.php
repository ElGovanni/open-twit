<?php

namespace App\Tests\Api;

use App\Controller\SecurityController;
use App\DataFixtures\User\UserFixtures;
use App\Entity\User\User;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;
use Symfony\Component\Uid\Uuid;

class SecurityCest
{
    public function _before(ApiTester $I)
    {
        $I->loadFixtures(UserFixtures::class);
    }

    public function testUserCanLogin(ApiTester $I)
    {
        $id = Uuid::fromString(UserFixtures::LOGIN_TEST)->toBinary();
        /** @var User $user */
        $user = $I->grabEntityFromRepository(User::class, [
            'id' => $id,
        ]);

        $I->sendPost(SecurityController::ROUTE_LOGIN, [
            'username' => $user->getEmail(),
            'password' => '123456',
        ]);

        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function testUserCanRefreshToken(ApiTester $I)
    {
        $auth = $I->authenticateUser();

        $I->sendPost(SecurityController::ROUTE_TOKEN_REFRESH, [
            'refreshToken' => $auth['refreshToken'],
        ]);

        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
