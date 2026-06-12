<?php

declare(strict_types=1);

namespace Fulll\App\Command\RegisterVehicle;

use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\PlateNumber;

final readonly class RegisterVehicleCommand
{
    public function __construct(
        public FleetId $fleetId,
        public PlateNumber $plateNumber,
    ) {
    }
}
