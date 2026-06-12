<?php

declare(strict_types=1);

namespace Fulll\Tests\Unit\App\Command\LocalizeVehicle;

use Fulll\App\Command\LocalizeVehicle\LocalizeVehicleCommand;
use Fulll\App\Command\LocalizeVehicle\LocalizeVehicleCommandHandler;
use Fulll\Domain\Aggregate\Fleet;
use Fulll\Domain\Exception\FleetNotFoundException;
use Fulll\Domain\Repository\FleetRepository;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\Location;
use Fulll\Domain\ValueObject\PlateNumber;
use Fulll\Domain\ValueObject\UserId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LocalizeVehicleCommandHandlerTest extends TestCase
{
    private FleetRepository&MockObject $fleetRepository;
    private LocalizeVehicleCommandHandler $sut;

    public function setUp(): void
    {
        $this->fleetRepository = self::createMock(FleetRepository::class);
        $this->sut = new LocalizeVehicleCommandHandler($this->fleetRepository);
    }

    public function testLocalizesVehicleIntoFleet(): void
    {
        $fleetId = new FleetId('fleet-1');
        $plateNumber = new PlateNumber('AB-123-CD');
        $fleet = new Fleet($fleetId, new UserId('user-1'));
        $fleet->register($plateNumber);

        $this->fleetRepository
            ->method('find')
            ->willReturn($fleet);
        $this->fleetRepository
            ->expects(self::once())
            ->method('save')
            ->with($fleet);

        $this->sut->handle(
            new LocalizeVehicleCommand($fleetId, $plateNumber, new Location(48.85, 2.35)),
        );
    }

    public function testFailsWhenFleetDoesNotExist(): void
    {
        $this->fleetRepository
            ->method('find')
            ->willReturn(null);
        $this->fleetRepository
            ->expects(self::never())
            ->method('save');

        $this->expectException(FleetNotFoundException::class);

        $this->sut->handle(
            new LocalizeVehicleCommand(
                new FleetId('missing'),
                new PlateNumber('AB-123-CD'),
                new Location(48.85, 2.35),
            ),
        );
    }
}
