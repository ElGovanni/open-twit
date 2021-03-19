<?php

namespace App\OpenApi;

use App\Controller\SecurityController;
use App\OpenApi\Model\AuthToken;
use App\OpenApi\Model\RefreshToken;

#[Doc(
    tag: 'Auth',
    url: SecurityController::ROUTE_TOKEN_REFRESH,
    method: 'POST',
)]
class RefreshTokenActionDoc implements RequestInterface, ResponseInterface
{
    public function getRequestClass(): string
    {
        return RefreshToken::class;
    }

    public function getResponseClass(): string
    {
        return AuthToken::class;
    }
}
