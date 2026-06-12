<?php

declare(strict_types=1);

namespace Fulll\App\Command\LocalizeVehicle;

use Fulll\Domain\Exception\FleetNotFoundException;
use Fulll\Domain\Exception\VehicleNotRegisteredException;
use Fulll\Domain\Repository\FleetRepository;

final readonly class LocalizeVehicleCommandHandler
{
    public function __construct(private FleetRepository $fleetRepository)
    {
    }

    /**
     * @throws FleetNotFoundException
     * @throws VehicleNotRegisteredException
     */
    public function handle(LocalizeVehicleCommand $command): void
    {
        $fleet = $this->fleetRepository->find($command->fleetId);

        if ($fleet === null) {
            throw FleetNotFoundException::withId($command->fleetId);
        }

        $fleet->localize($command->plateNumber, $command->location);

        $this->fleetRepository->save($fleet);
    }
}
