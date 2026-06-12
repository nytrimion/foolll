<?php

declare(strict_types=1);

namespace Fulll\Tests\Unit\Infra\InMemory;

use Fulll\Domain\Aggregate\Fleet;
use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\UserId;
use Fulll\Infra\InMemory\InMemoryFleetRepository;
use PHPUnit\Framework\TestCase;

final class InMemoryFleetRepositoryTest extends TestCase
{
    private InMemoryFleetRepository $sut;

    public function setUp(): void
    {
        $this->sut = new InMemoryFleetRepository();
    }

    public function testSavesAndRetrievesFleet(): void
    {
        $fleet = new Fleet(new FleetId('fleet-1'), new UserId('user-1'));

        $this->sut->save($fleet);

        self::assertSame($fleet, $this->sut->find(new FleetId('fleet-1')));
    }

    public function testReturnsNullForUnknownFleet(): void
    {
        self::assertNull($this->sut->find(new FleetId('missing')));
    }
}
