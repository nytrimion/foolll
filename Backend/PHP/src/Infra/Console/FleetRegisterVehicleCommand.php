<?php

declare(strict_types=1);

namespace Fulll\Infra\Console;

use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommand;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommandHandler;
use Fulll\Domain\Repository\FleetRepository;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\PlateNumber;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'register-vehicle', description: 'Register a vehicle into a fleet.')]
final class FleetRegisterVehicleCommand extends Command
{
    public function __construct(private readonly FleetRepository $fleetRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('fleetId', InputArgument::REQUIRED, 'The fleet to register the vehicle into.');
        $this->addArgument('plateNumber', InputArgument::REQUIRED, 'The plate number of the vehicle.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            new RegisterVehicleCommandHandler($this->fleetRepository)->handle(
                new RegisterVehicleCommand(
                    new FleetId((string) $input->getArgument('fleetId')),
                    new PlateNumber((string) $input->getArgument('plateNumber')),
                ),
            );
        } catch (\Throwable $throwable) {
            new SymfonyStyle($input, $output)->error($throwable->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
