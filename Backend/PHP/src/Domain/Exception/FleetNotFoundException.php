<?php

declare(strict_types=1);

namespace Fulll\Domain\Exception;

use Fulll\Domain\ValueObject\FleetId;

final class FleetNotFoundException extends \DomainException
{
    public static function withId(FleetId $fleetId): self
    {
        return new self("Fleet \"{$fleetId->value}\" does not exist.");
    }
}
