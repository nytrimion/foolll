<?php

declare(strict_types=1);

namespace Fulll\Domain\Exception;

use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\PlateNumber;

final class VehicleNotRegisteredException extends \DomainException
{
    public static function in(FleetId $fleetId, PlateNumber $plateNumber): self
    {
        return new self("Vehicle \"{$plateNumber->value}\" is not registered into fleet \"{$fleetId->value}\".");
    }
}
