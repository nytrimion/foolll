<?php

declare(strict_types=1);

namespace Fulll\App\Command\LocalizeVehicle;

use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\Location;
use Fulll\Domain\ValueObject\PlateNumber;

final readonly class LocalizeVehicleCommand
{
    public function __construct(
        public FleetId $fleetId,
        public PlateNumber $plateNumber,
        public Location $location,
    ) {
    }
}
