<?php

namespace App\OpenApi;

use App\Controller\SecurityController;
use App\OpenApi\Model\AuthToken;
use App\OpenApi\Model\Login;

#[Doc(
    tag: 'Auth',
    url: SecurityController::ROUTE_LOGIN,
    method: 'POST',
)]
class LoginActionDoc implements RequestInterface, ResponseInterface
{
    public function getRequestClass(): string
    {
        return Login::class;
    }

    public function getResponseClass(): string
    {
        return AuthToken::class;
    }
}
