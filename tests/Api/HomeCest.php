<?php

namespace App\Tests\Api;

use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class HomeCest
{
    public function index(ApiTester $I)
    {
        $I->sendGet('');
        $I->seeResponseCodeIs(HttpCode::OK);
    }
}
