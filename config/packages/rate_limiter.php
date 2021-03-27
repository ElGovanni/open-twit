<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void
{
    $containerConfigurator->extension('framework', [
        'rate_limiter' => [
            'confirm_token' => [
                'policy' => 'fixed_window',
                'limit' => 3,
                'interval' => '1 minute'
            ]
        ]
    ]);
};
