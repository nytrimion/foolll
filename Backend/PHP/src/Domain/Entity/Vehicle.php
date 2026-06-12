<?php

declare(strict_types=1);

namespace Fulll\Domain\Entity;

use Fulll\Domain\Exception\VehicleAlreadyParkedException;
use Fulll\Domain\ValueObject\Location;
use Fulll\Domain\ValueObject\PlateNumber;

final class Vehicle
{
    public function __construct(
        public readonly PlateNumber $plateNumber,
        public private(set) ?Location $location = null,
    ) {
    }

    /**
     * @throws VehicleAlreadyParkedException
     */
    public function localize(Location $location): void
    {
        if ($this->location?->equals($location)) {
            throw VehicleAlreadyParkedException::at($this->plateNumber, $location);
        }

        $this->location = $location;
    }
}
