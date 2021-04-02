<?php

namespace App\Tests\Api\User;

use App\DataFixtures\User\UserFixtures;
use App\Entity\User\User;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class ProfilePictureCest
{
    public function _before(ApiTester $I)
    {
        $I->loadFixtures(UserFixtures::class);
    }

    public function create(ApiTester $I)
    {
        $id = Uuid::fromString(UserFixtures::CREATE_FILE_TEST)->toBinary();
        /** @var User $user */
        $user = $I->grabEntityFromRepository(User::class, [
            'id' => $id,
        ]);
        $auth = $I->authenticateUser($user->getEmail());

        $I->amBearerAuthenticated($auth['token']);

        $I->sendPost('/users/profile_pictures', null, [
            'file' => new UploadedFile(__DIR__ . '/../../_data/profile_picture.jpeg', 'profile_picture.jpeg'),
        ]);

        $I->seeResponseCodeIs(HttpCode::CREATED);

        $I->seeResponseMatchesJsonType([
            'id' => 'string',
            'absolutePath' => 'string',
        ]);
    }
}
