<?php

declare(strict_types=1);

namespace Fulll\Infra\Console;

use Fulll\App\Command\LocalizeVehicle\LocalizeVehicleCommand;
use Fulll\App\Command\LocalizeVehicle\LocalizeVehicleCommandHandler;
use Fulll\Domain\Repository\FleetRepository;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\Location;
use Fulll\Domain\ValueObject\PlateNumber;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'localize-vehicle', description: 'Set the last known location of a vehicle in a fleet.')]
final class FleetLocalizeVehicleCommand extends Command
{
    public function __construct(private readonly FleetRepository $fleetRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('fleetId', InputArgument::REQUIRED, 'The fleet the vehicle belongs to.');
        $this->addArgument('plateNumber', InputArgument::REQUIRED, 'The plate number of the vehicle.');
        $this->addArgument('lat', InputArgument::REQUIRED, 'The latitude.');
        $this->addArgument('lng', InputArgument::REQUIRED, 'The longitude.');
        $this->addArgument('alt', InputArgument::OPTIONAL, 'The altitude.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $latitude = $this->getNumericArgument($input, 'lat');
            $longitude = $this->getNumericArgument($input, 'lng');
            $altitude = $input->getArgument('alt') !== null ? $this->getNumericArgument($input, 'alt') : null;
        } catch (\Throwable $throwable) {
            $io->error($throwable->getMessage());

            return Command::INVALID;
        }

        try {
            new LocalizeVehicleCommandHandler($this->fleetRepository)->handle(
                new LocalizeVehicleCommand(
                    new FleetId((string) $input->getArgument('fleetId')),
                    new PlateNumber((string) $input->getArgument('plateNumber')),
                    new Location($latitude, $longitude, $altitude),
                ),
            );
        } catch (\Throwable $throwable) {
            $io->error($throwable->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function getNumericArgument(InputInterface $input, string $name): float
    {
        $value = $input->getArgument($name);

        if (!is_numeric($value)) {
            throw new \ValueError("The \"{$name}\" argument must be a number.");
        }

        return (float) $value;
    }
}
