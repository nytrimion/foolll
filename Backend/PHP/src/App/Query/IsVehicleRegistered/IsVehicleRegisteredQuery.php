<?php

declare(strict_types=1);

namespace Fulll\App\Query\IsVehicleRegistered;

use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\PlateNumber;

final readonly class IsVehicleRegisteredQuery
{
    public function __construct(
        public FleetId $fleetId,
        public PlateNumber $plateNumber,
    ) {
    }
}
