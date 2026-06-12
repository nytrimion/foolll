<?php

declare(strict_types=1);

namespace Fulll\App\Command\CreateFleet;

use Fulll\Domain\ValueObject\FleetId;
use Fulll\Domain\ValueObject\UserId;

final readonly class CreateFleetCommand
{
    public function __construct(
        public FleetId $fleetId,
        public UserId $userId,
    ) {
    }
}
