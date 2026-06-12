<?php

declare(strict_types=1);

namespace Fulll\Infra\InMemory;

use Fulll\Domain\Aggregate\Fleet;
use Fulll\Domain\Repository\FleetRepository;
use Fulll\Domain\ValueObject\FleetId;

final class InMemoryFleetRepository implements FleetRepository
{
    /** @var array<string, Fleet> */
    private array $fleets = [];

    public function save(Fleet $fleet): void
    {
        $this->fleets[$fleet->id->value] = $fleet;
    }

    public function find(FleetId $fleetId): ?Fleet
    {
        return $this->fleets[$fleetId->value] ?? null;
    }
}
