<?php

declare(strict_types=1);

namespace Fulll\Infra\Console;

use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Command\CreateFleet\CreateFleetCommandHandler;
use Fulll\Domain\Repository\FleetRepository;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\UserId;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'create', description: 'Create a fleet for a user and output its id.')]
final class FleetCreateCommand extends Command
{
    public function __construct(private readonly FleetRepository $fleetRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('userId', InputArgument::REQUIRED, 'The user owning the fleet.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fleetId = new FleetId(Uuid::v7()->toRfc4122());

        try {
            new CreateFleetCommandHandler($this->fleetRepository)->handle(
                new CreateFleetCommand($fleetId, new UserId((string) $input->getArgument('userId'))),
            );
        } catch (\Throwable $throwable) {
            new SymfonyStyle($input, $output)->error($throwable->getMessage());

            return Command::FAILURE;
        }

        $output->writeln($fleetId->value);

        return Command::SUCCESS;
    }
}
