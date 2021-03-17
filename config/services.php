<?php

declare(strict_types=1);

use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->instanceof(EventSubscriber::class)
        ->tag('doctrine.event_subscriber');

    $services->load('App\\', __DIR__ . '/../src/')
        ->exclude([__DIR__ . '/../src/DependencyInjection/', __DIR__ . '/../src/Entity/', __DIR__ . '/../src/Kernel.php', __DIR__ . '/../src/Tests/']);

    $services->load('App\Controller\\', __DIR__ . '/../src/Controller/')
        ->tag('controller.service_arguments');
};
