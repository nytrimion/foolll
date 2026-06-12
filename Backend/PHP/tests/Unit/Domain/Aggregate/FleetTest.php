<?php

declare(strict_types=1);

namespace Fulll\Tests\Unit\Domain\Aggregate;

use Fulll\Domain\Aggregate\Fleet;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\PlateNumber;
use Fulll\Domain\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

final class FleetTest extends TestCase
{
    public function testHasNoVehicleByDefault(): void
    {
        $fleet = new Fleet(new FleetId('fleet-1'), new UserId('user-1'));

        self::assertFalse($fleet->hasVehicle(new PlateNumber('AB-123-CD')));
    }

    public function testRegistersVehicle(): void
    {
        $fleet = new Fleet(new FleetId('fleet-1'), new UserId('user-1'));
        $plateNumber = new PlateNumber('AB-123-CD');

        $fleet->register($plateNumber);

        self::assertTrue($fleet->hasVehicle($plateNumber));
    }
}
