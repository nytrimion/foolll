<?php

declare(strict_types=1);

namespace Fulll\Tests\Unit\App\Query\IsVehicleRegistered;

use Fulll\App\Query\IsVehicleRegistered\IsVehicleRegisteredQuery;
use Fulll\App\Query\IsVehicleRegistered\IsVehicleRegisteredQueryHandler;
use Fulll\Domain\Aggregate\Fleet;
use Fulll\Domain\Exception\FleetNotFoundException;
use Fulll\Domain\Repository\FleetRepository;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\PlateNumber;
use Fulll\Domain\ValueObject\UserId;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class IsVehicleRegisteredQueryHandlerTest extends TestCase
{
    private FleetRepository&Stub $fleetRepository;
    private IsVehicleRegisteredQueryHandler $sut;

    public function setUp(): void
    {
        $this->fleetRepository = self::createStub(FleetRepository::class);
        $this->sut = new IsVehicleRegisteredQueryHandler($this->fleetRepository);
    }

    public function testReturnsTrueWhenVehicleIsRegistered(): void
    {
        $fleetId = new FleetId('fleet-1');
        $plateNumber = new PlateNumber('AB-123-CD');
        $fleet = new Fleet($fleetId, new UserId('user-1'));
        $fleet->register($plateNumber);

        $this->fleetRepository->method('find')->willReturn($fleet);

        self::assertTrue($this->sut->handle(new IsVehicleRegisteredQuery($fleetId, $plateNumber)));
    }

    public function testReturnsFalseWhenVehicleIsNotRegistered(): void
    {
        $fleetId = new FleetId('fleet-1');
        $fleet = new Fleet($fleetId, new UserId('user-1'));

        $this->fleetRepository->method('find')->willReturn($fleet);

        self::assertFalse(
            $this->sut->handle(new IsVehicleRegisteredQuery($fleetId, new PlateNumber('AB-123-CD'))),
        );
    }

    public function testFailsWhenFleetDoesNotExist(): void
    {
        $this->fleetRepository->method('find')->willReturn(null);

        $this->expectException(FleetNotFoundException::class);

        $this->sut->handle(
            new IsVehicleRegisteredQuery(new FleetId('missing'), new PlateNumber('AB-123-CD')),
        );
    }
}
