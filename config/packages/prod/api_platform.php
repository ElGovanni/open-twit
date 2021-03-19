<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('api_platform', [
        'enable_docs' => false,
        'enable_swagger' => false,
        'enable_swagger_ui' => false,
        'enable_entrypoint' => false,
    ]);
};
