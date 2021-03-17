<?php

declare(strict_types=1);

use App\Controller\SecurityController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->add('api_login_check', SecurityController::ROUTE_LOGIN);

    $routingConfigurator->add('api_refresh_token', SecurityController::ROUTE_TOKEN_REFRESH)
        ->controller('gesdinet.jwtrefreshtoken::refresh')
    ;
};
