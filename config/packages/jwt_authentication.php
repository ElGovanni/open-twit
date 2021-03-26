<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function(ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('lexik_jwt_authentication', [
        'secret_key' => '%env(JWT_PASSPHRASE)%',
        'encoder' => [
            'signature_algorithm' => 'HS256'
        ],
        'user_identity_field' => 'email',
    ]);
    $containerConfigurator->extension('gesdinet_jwt_refresh_token', [
        'single_use' => true,
        'token_parameter_name' => 'refreshToken',
        'user_identity_field' => 'email',
        'user_provider' => 'security.user.provider.concrete.app_user_provider'
    ]);
};
