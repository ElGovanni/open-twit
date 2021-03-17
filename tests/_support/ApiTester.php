<?php

namespace App\Tests;

use App\Controller\SecurityController;
use Codeception\Scenario;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->haveHttpHeader('Accept', 'application/json');
    }

    public function authenticateUser(string $email = 'User0@localhost.domain', string $password = '123456'): array
    {
        $this->sendPost(SecurityController::ROUTE_LOGIN, [
            'username' => $email,
            'password' => $password,
        ]);

        return json_decode($this->grabResponse(), true);
    }
}
