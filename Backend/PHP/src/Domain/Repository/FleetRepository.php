<?php

declare(strict_types=1);

namespace Fulll\Domain\Repository;

use Fulll\Domain\Aggregate\Fleet;
use Fulll\Domain\ValueObject\FleetId;

interface FleetRepository
{
    public function save(Fleet $fleet): void;

    public function find(FleetId $fleetId): ?Fleet;
}
