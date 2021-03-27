<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'transports' => [
                'async' => '%env(MESSENGER_TRANSPORT_DSN)%'
            ],
            'routing' => [
                SendEmailMessage::class => 'async'
            ],
        ],
    ]);
};
