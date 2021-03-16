<?php

namespace App\Tests\Extension;

use Codeception\Events;
use Codeception\Module\Symfony;
use Codeception\Platform\Extension;
use Exception;

class RecreateDatabase extends Extension
{
    public static array $events = [
        Events::SUITE_BEFORE => 'beforeSuite',
    ];

    public function beforeSuite()
    {
        try {
            /** @var Symfony $symfony */
            $symfony = $this->getModule('Symfony');

            $this->writeln('Recreating the DB...');

            $symfony->runSymfonyConsoleCommand('doctrine:database:drop', [
                '--env' => 'test',
                '--force' => null,
            ]);
            $symfony->runSymfonyConsoleCommand('doctrine:schema:create', [
                '--env' => 'test',
            ]);

            $this->writeln('Test database recreated');
        } catch (Exception $e) {
            $this->writeln(
                sprintf(
                    'An error occurred whilst rebuilding the test database: %s',
                    $e->getMessage()
                )
            );
        }
    }
}
