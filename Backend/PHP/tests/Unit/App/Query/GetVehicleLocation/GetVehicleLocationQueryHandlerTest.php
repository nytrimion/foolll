<?php

declare(strict_types=1);

namespace Fulll\Tests\Unit\App\Query\GetVehicleLocation;

use Fulll\App\Query\GetVehicleLocation\GetVehicleLocationQuery;
use Fulll\App\Query\GetVehicleLocation\GetVehicleLocationQueryHandler;
use Fulll\Domain\Aggregate\Fleet;
use Fulll\Domain\Exception\FleetNotFoundException;
use Fulll\Domain\Exception\VehicleNotRegisteredException;
use Fulll\Domain\Repository\FleetRepository;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\Location;
use Fulll\Domain\ValueObject\PlateNumber;
use Fulll\Domain\ValueObject\UserId;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class GetVehicleLocationQueryHandlerTest extends TestCase
{
    private FleetRepository&Stub $fleetRepository;
    private GetVehicleLocationQueryHandler $sut;

    public function setUp(): void
    {
        $this->fleetRepository = self::createStub(FleetRepository::class);
        $this->sut = new GetVehicleLocationQueryHandler($this->fleetRepository);
    }

    public function testReturnsVehicleLocation(): void
    {
        $fleetId = new FleetId('fleet-1');
        $plateNumber = new PlateNumber('AB-123-CD');
        $location = new Location(48.85, 2.35);
        $fleet = new Fleet($fleetId, new UserId('user-1'));
        $fleet->register($plateNumber);
        $fleet->localize($plateNumber, $location);

        $this->fleetRepository->method('find')->willReturn($fleet);

        self::assertSame(
            $location,
            $this->sut->handle(new GetVehicleLocationQuery($fleetId, $plateNumber)),
        );
    }

    public function testFailsWhenVehicleIsUnregistered(): void
    {
        $fleetId = new FleetId('fleet-1');
        $fleet = new Fleet($fleetId, new UserId('user-1'));

        $this->fleetRepository->method('find')->willReturn($fleet);

        $this->expectException(VehicleNotRegisteredException::class);

        $this->sut->handle(new GetVehicleLocationQuery($fleetId, new PlateNumber('AB-123-CD')));
    }

    public function testFailsWhenFleetDoesNotExist(): void
    {
        $this->fleetRepository->method('find')->willReturn(null);

        $this->expectException(FleetNotFoundException::class);

        $this->sut->handle(
            new GetVehicleLocationQuery(new FleetId('missing'), new PlateNumber('AB-123-CD')),
        );
    }
}
