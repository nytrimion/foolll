<?php

declare(strict_types=1);

namespace Fulll\Domain\Entity;

use Fulll\Domain\ValueObject\PlateNumber;

final class Vehicle
{
    public function __construct(public readonly PlateNumber $plateNumber)
    {
    }
}
