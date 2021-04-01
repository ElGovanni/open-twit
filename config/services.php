<?php

declare(strict_types=1);

use ApiPlatform\Core\JsonSchema\SchemaFactoryInterface;
use App\OpenApi\OpenApiFactory;
use App\OpenApi\RequestInterface;
use App\Security\ConfirmTokenGenerator\ConfirmTokenGeneratorInterface;
use App\Security\ConfirmTokenGenerator\SimpleNumericalTokenGenerator;
use App\Storage\FilesystemAdapterFactory;
use Doctrine\Common\EventSubscriber;
use Imagine\Gd\Imagine;
use Imagine\Image\ImagineInterface;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()
        ->set('app.mail.noreply', '%env(resolve:MAILER_NO_REPLY)%');
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('bool $userConfirmEmail', '%env(resolve:USER_CONFIRM_EMAIL)%')
        ->bind('string $environment', '%kernel.environment%')
        ->bind('string $mailNoReply', '%app.mail.noreply%')
        ->bind('string $cacheDir', '%kernel.cache_dir%')
    ;

    $services->instanceof(EventSubscriber::class)
        ->tag('doctrine.event_subscriber');

    $services->instanceof(RequestInterface::class)
        ->tag('api_platform.extended_docs');

    $services->load('App\\', __DIR__ . '/../src/')
        ->exclude([__DIR__ . '/../src/DependencyInjection/', __DIR__ . '/../src/Entity/', __DIR__ . '/../src/Kernel.php', __DIR__ . '/../src/Tests/']);

    $services->load('App\Controller\\', __DIR__ . '/../src/Controller/')
        ->tag('controller.service_arguments');

    $services->alias(ConfirmTokenGeneratorInterface::class, SimpleNumericalTokenGenerator::class);

    $services->set(ImagineInterface::class)
        ->class(Imagine::class);

    $services->set(Filesystem::class)
        ->class(Filesystem::class);

    $services->alias(FilesystemInterface::class, Filesystem::class);

    $services->set(AdapterInterface::class)
        ->factory([FilesystemAdapterFactory::class, 'createAdapter'])
        ->args([
            '%kernel.environment%',
            service(UrlGeneratorInterface::class),
            '%kernel.project_dir%/public/uploads',
            '%env(GCLOUD_PROJECT_ID)%',
            '%kernel.project_dir%/gcloud-service-key.json',
            '%env(GCLOUD_BUCKET)%',
        ])
    ;

    $services->set(OpenApiFactory::class)
        ->decorate('api_platform.openapi.factory')
        ->args([
            service('.inner'),
            service(SchemaFactoryInterface::class),
            tagged_iterator('api_platform.extended_docs')
        ])
    ;
};
