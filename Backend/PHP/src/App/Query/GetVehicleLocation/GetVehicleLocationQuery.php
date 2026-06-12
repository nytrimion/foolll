<?php

declare(strict_types=1);

namespace Fulll\App\Query\GetVehicleLocation;

use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\PlateNumber;

final readonly class GetVehicleLocationQuery
{
    public function __construct(
        public FleetId $fleetId,
        public PlateNumber $plateNumber,
    ) {
    }
}
