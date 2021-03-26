<?php

declare(strict_types=1);

use App\Controller\SecurityController;
use App\Entity\User\User;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension('security', [
        'encoders' => [
            User::class => [
                'algorithm' => 'auto'
            ]
        ],
        'providers' => [
            'app_user_provider' => [
                'entity' => [
                    'class' => User::class,
                    'property' => 'email'
                ],
            ]
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'token' => [
                'pattern' => '^/token',
                'stateless' => true,
                'anonymous' => true
            ],
            'api' => [
                'stateless' => true,
                'anonymous' => true,
                'json_login' => [
                    'check_path' => SecurityController::ROUTE_LOGIN,
                    'success_handler' => 'lexik_jwt_authentication.handler.authentication_success',
                    'failure_handler' => 'lexik_jwt_authentication.handler.authentication_failure',
                    'username_path' => 'login',
                ],
                'guard' => [
                    'authenticators' => [
                        'lexik_jwt_authentication.jwt_token_authenticator'
                    ]
                ]
            ],
        ],
        'access_control' => [
            ['path' => '^/token/refresh', 'roles' => 'IS_AUTHENTICATED_ANONYMOUSLY'],
            ['path' => '^/login', 'roles' => 'IS_AUTHENTICATED_ANONYMOUSLY'],
            ['path' => '^/', 'roles' => 'IS_AUTHENTICATED_ANONYMOUSLY'],
        ],
    ]);
};
