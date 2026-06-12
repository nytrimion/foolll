<?php

declare(strict_types=1);

namespace Fulll\Tests\Unit\App\Command\CreateFleet;

use Fulll\App\Command\CreateFleet\CreateFleetCommand;
use Fulll\App\Command\CreateFleet\CreateFleetCommandHandler;
use Fulll\Domain\Repository\FleetRepository;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\UserId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateFleetCommandHandlerTest extends TestCase
{
    private FleetRepository&MockObject $fleetRepository;
    private CreateFleetCommandHandler $sut;

    public function setUp(): void
    {
        $this->fleetRepository = self::createMock(FleetRepository::class);
        $this->sut = new CreateFleetCommandHandler($this->fleetRepository);
    }

    public function testCreatesAndPersistsFleet(): void
    {
        $fleetId = new FleetId('fleet-1');

        $this->fleetRepository->expects(self::once())->method('save');

        $this->sut->handle(
            new CreateFleetCommand($fleetId, new UserId('user-1')),
        );
    }
}
