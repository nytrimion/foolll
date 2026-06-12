<?php

declare(strict_types=1);

namespace Fulll\Tests\Unit\Domain\ValueObject;

use Fulll\Domain\ValueObject\Location;
use PHPUnit\Framework\TestCase;

final class LocationTest extends TestCase
{
    public function testEqualsWhenCoordinatesAreEqual(): void
    {
        self::assertTrue(new Location(48.85, 2.35)->equals(new Location(48.85, 2.35)));
    }

    public function testEqualsWhenAltitudeDiffers(): void
    {
        self::assertTrue(new Location(48.85, 2.35, 35.0)->equals(new Location(48.85, 2.35)));
    }

    public function testDiffersWhenLatitudeDiffers(): void
    {
        self::assertFalse(new Location(48.85, 2.35)->equals(new Location(45.76, 2.35)));
    }

    public function testDiffersWhenLongitudeDiffers(): void
    {
        self::assertFalse(new Location(48.85, 2.35)->equals(new Location(48.85, 4.83)));
    }
}
