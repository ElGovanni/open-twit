<?php

declare(strict_types=1);

use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use App\OpenApi\OpenApiFactory;
use App\OpenApi\RequestInterface;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->instanceof(EventSubscriber::class)
        ->tag('doctrine.event_subscriber');

    $services->instanceof(RequestInterface::class)
        ->tag('api_platform.extended_docs');

    $services->load('App\\', __DIR__ . '/../src/')
        ->exclude([__DIR__ . '/../src/DependencyInjection/', __DIR__ . '/../src/Entity/', __DIR__ . '/../src/Kernel.php', __DIR__ . '/../src/Tests/']);

    $services->load('App\Controller\\', __DIR__ . '/../src/Controller/')
        ->tag('controller.service_arguments');

    $services->set(OpenApiFactory::class)
        ->decorate('api_platform.openapi.factory')
        ->args([
            service('.inner'),
            service(SchemaFactoryInterface::class),
            tagged_iterator('api_platform.extended_docs')
        ])
    ;
};
