<?php

declare(strict_types=1);

namespace Fulll\Tests\Unit\App\Command\RegisterVehicle;

use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommand;
use Fulll\App\Command\RegisterVehicle\RegisterVehicleCommandHandler;
use Fulll\Domain\Aggregate\Fleet;
use Fulll\Domain\Exception\FleetNotFoundException;
use Fulll\Domain\Repository\FleetRepository;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\PlateNumber;
use Fulll\Domain\ValueObject\UserId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RegisterVehicleCommandHandlerTest extends TestCase
{
    private FleetRepository&MockObject $fleetRepository;
    private RegisterVehicleCommandHandler $sut;

    public function setUp(): void
    {
        $this->fleetRepository = self::createMock(FleetRepository::class);
        $this->sut = new RegisterVehicleCommandHandler($this->fleetRepository);
    }

    public function testRegistersVehicleIntoFleet(): void
    {
        $fleetId = new FleetId('fleet-1');
        $fleet = new Fleet($fleetId, new UserId('user-1'));
        $plateNumber = new PlateNumber('AB-123-CD');

        $this->fleetRepository
            ->method('find')
            ->willReturn($fleet);
        $this->fleetRepository
            ->expects(self::once())
            ->method('save')
            ->with($fleet);

        $this->sut->handle(new RegisterVehicleCommand($fleetId, $plateNumber));
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
            new RegisterVehicleCommand(new FleetId('missing'), new PlateNumber('AB-123-CD')),
        );
    }
}
