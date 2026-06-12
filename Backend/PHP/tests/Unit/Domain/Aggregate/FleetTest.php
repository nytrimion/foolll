<?php

declare(strict_types=1);

namespace Fulll\Tests\Unit\Domain\Aggregate;

use Fulll\Domain\Aggregate\Fleet;
use Fulll\Domain\Exception\VehicleAlreadyRegisteredException;
use Fulll\Domain\Exception\VehicleNotRegisteredException;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\Location;
use Fulll\Domain\ValueObject\PlateNumber;
use Fulll\Domain\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

final class FleetTest extends TestCase
{
    private Fleet $sut;

    protected function setUp(): void
    {
        $this->sut = new Fleet(
            new FleetId('fleet-1'),
            new UserId('user-1'),
        );
    }

    public function testHasNoVehicleByDefault(): void
    {
        self::assertFalse($this->sut->hasVehicle(new PlateNumber('AB-123-CD')));
    }

    public function testRegistersVehicle(): void
    {
        $plateNumber = new PlateNumber('AB-123-CD');

        $this->sut->register($plateNumber);

        self::assertTrue($this->sut->hasVehicle($plateNumber));
    }

    public function testFailsToRegisterVehicleWhenAlreadyRegistered(): void
    {
        $plateNumber = new PlateNumber('AB-123-CD');
        $this->sut->register($plateNumber);

        $this->expectException(VehicleAlreadyRegisteredException::class);

        $this->sut->register($plateNumber);
    }

    public function testLocalizesVehicleWhenRegistered(): void
    {
        $plateNumber = new PlateNumber('AB-123-CD');
        $this->sut->register($plateNumber);
        $location = new Location(48.85, 2.35);

        $this->sut->localize($plateNumber, $location);

        self::assertSame($location, $this->sut->locationOf($plateNumber));
    }

    public function testFailsToLocalizeVehicleWhenUnregistered(): void
    {
        $this->expectException(VehicleNotRegisteredException::class);

        $this->sut->localize(new PlateNumber('AB-123-CD'), new Location(48.85, 2.35));
    }

    public function testFailsToReadVehicleLocationWhenUnregistered(): void
    {
        $this->expectException(VehicleNotRegisteredException::class);

        $this->sut->locationOf(new PlateNumber('AB-123-CD'));
    }
}
