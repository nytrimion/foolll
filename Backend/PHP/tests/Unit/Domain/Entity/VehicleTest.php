<?php

declare(strict_types=1);

namespace Fulll\Tests\Unit\Domain\Entity;

use Fulll\Domain\Entity\Vehicle;
use Fulll\Domain\ValueObject\Location;
use Fulll\Domain\ValueObject\PlateNumber;
use PHPUnit\Framework\TestCase;

final class VehicleTest extends TestCase
{
    public function testHasNoLocationByDefault(): void
    {
        $vehicle = new Vehicle(new PlateNumber('AB-123-CD'));

        self::assertNull($vehicle->location);
    }

    public function testRecordsLocationWhenLocalized(): void
    {
        $vehicle = new Vehicle(new PlateNumber('AB-123-CD'));
        $location = new Location(48.85, 2.35);

        $vehicle->localize($location);

        self::assertSame($location, $vehicle->location);
    }

    public function testUpdatesLocationWhenLocalizedAgain(): void
    {
        $vehicle = new Vehicle(new PlateNumber('AB-123-CD'));
        $vehicle->localize(new Location(48.85, 2.35));

        $newLocation = new Location(45.76, 4.83);
        $vehicle->localize($newLocation);

        self::assertSame($newLocation, $vehicle->location);
    }
}
