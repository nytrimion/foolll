<?php

declare(strict_types=1);

namespace Fulll\App\Command\RegisterVehicle;

use Fulll\Domain\Exception\FleetNotFoundException;
use Fulll\Domain\Repository\FleetRepository;

final readonly class RegisterVehicleCommandHandler
{
    public function __construct(private FleetRepository $fleetRepository)
    {
    }

    /**
     * @throws FleetNotFoundException
     */
    public function handle(RegisterVehicleCommand $command): void
    {
        $fleet = $this->fleetRepository->find($command->fleetId);

        if ($fleet === null) {
            throw FleetNotFoundException::withId($command->fleetId);
        }

        $fleet->register($command->plateNumber);

        $this->fleetRepository->save($fleet);
    }
}
