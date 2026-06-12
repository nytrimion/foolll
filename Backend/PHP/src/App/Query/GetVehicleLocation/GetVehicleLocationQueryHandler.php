<?php

declare(strict_types=1);

namespace Fulll\App\Query\GetVehicleLocation;

use Fulll\Domain\Exception\FleetNotFoundException;
use Fulll\Domain\Exception\VehicleNotRegisteredException;
use Fulll\Domain\Repository\FleetRepository;
use Fulll\Domain\ValueObject\Location;

final readonly class GetVehicleLocationQueryHandler
{
    public function __construct(private FleetRepository $fleetRepository)
    {
    }

    /**
     * @throws FleetNotFoundException
     * @throws VehicleNotRegisteredException
     */
    public function handle(GetVehicleLocationQuery $query): ?Location
    {
        $fleet = $this->fleetRepository->find($query->fleetId);

        if ($fleet === null) {
            throw FleetNotFoundException::withId($query->fleetId);
        }

        return $fleet->locationOf($query->plateNumber);
    }
}
