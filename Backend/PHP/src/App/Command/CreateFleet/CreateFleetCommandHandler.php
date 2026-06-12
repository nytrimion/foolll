<?php

declare(strict_types=1);

namespace Fulll\App\Command\CreateFleet;

use Fulll\Domain\Aggregate\Fleet;
use Fulll\Domain\Repository\FleetRepository;

final readonly class CreateFleetCommandHandler
{
    public function __construct(private FleetRepository $fleetRepository)
    {
    }

    public function handle(CreateFleetCommand $command): void
    {
        $this->fleetRepository->save(new Fleet($command->fleetId, $command->userId));
    }
}
