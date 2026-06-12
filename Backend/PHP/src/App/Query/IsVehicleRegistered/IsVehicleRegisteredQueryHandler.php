<?php

declare(strict_types=1);

namespace Fulll\App\Query\IsVehicleRegistered;

use Fulll\Domain\Exception\FleetNotFoundException;
use Fulll\Domain\Repository\FleetRepository;

final readonly class IsVehicleRegisteredQueryHandler
{
    public function __construct(private FleetRepository $fleetRepository)
    {
    }

    /**
     * @throws FleetNotFoundException
     */
    public function handle(IsVehicleRegisteredQuery $query): bool
    {
        $fleet = $this->fleetRepository->find($query->fleetId);

        if ($fleet === null) {
            throw FleetNotFoundException::withId($query->fleetId);
        }

        return $fleet->hasVehicle($query->plateNumber);
    }
}
