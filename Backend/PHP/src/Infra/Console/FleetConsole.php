<?php

declare(strict_types=1);

namespace Fulll\Infra\Console;

use Fulll\Domain\Repository\FleetRepository;
use Symfony\Component\Console\Application;

final class FleetConsole
{
    public static function application(FleetRepository $fleetRepository): Application
    {
        $application = new Application('fleet');

        $application->addCommands([
            new FleetCreateCommand($fleetRepository),
        ]);

        return $application;
    }
}
