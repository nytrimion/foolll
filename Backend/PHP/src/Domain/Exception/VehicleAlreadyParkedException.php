<?php

declare(strict_types=1);

namespace Fulll\Domain\Exception;

use Fulll\Domain\ValueObject\Location;
use Fulll\Domain\ValueObject\PlateNumber;

final class VehicleAlreadyParkedException extends \DomainException
{
    public static function at(PlateNumber $plateNumber, Location $location): self
    {
        return new self(
            "Vehicle \"{$plateNumber->value}\" is already parked at ({$location->latitude}, {$location->longitude}).",
        );
    }
}
