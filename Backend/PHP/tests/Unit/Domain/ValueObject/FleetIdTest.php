<?php

declare(strict_types=1);

namespace Fulll\Tests\Unit\Domain\ValueObject;

use Fulll\Domain\ValueObject\FleetId;
use PHPUnit\Framework\TestCase;

final class FleetIdTest extends TestCase
{
    public function testRejectsEmptyValue(): void
    {
        $this->expectException(\DomainException::class);

        new FleetId('   ');
    }

    public function testIsEqualToAnotherIdWithSameValue(): void
    {
        $fleetId = new FleetId('fleet-1');

        self::assertTrue($fleetId->equals(new FleetId('fleet-1')));
        self::assertFalse($fleetId->equals(new FleetId('fleet-2')));
    }
}
