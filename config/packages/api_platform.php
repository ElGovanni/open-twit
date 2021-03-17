<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('api_platform', [
        'collection' => [
            'pagination' => [
                'client_items_per_page' => true,
                'maximum_items_per_page' => 20,
            ]
        ],
        'mapping' => [
            'paths' => ['%kernel.project_dir%/src/Entity']
        ],
        'swagger' => [
            'versions' => [3],
            'api_keys' => [
                'apiKey' => [
                    'name' => 'Authorization',
                    'type' => 'header',
                ]
            ]
        ]
    ]);

    $containerConfigurator->extension('api_platform', [
        'patch_formats' => [
            'json' => ['application/merge-patch+json']
        ]
    ]);
};
