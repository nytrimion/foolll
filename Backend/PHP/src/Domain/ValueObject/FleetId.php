<?php

declare(strict_types=1);

namespace Fulll\Domain\ValueObject;

final readonly class FleetId
{
    public function __construct(public string $value)
    {
        if (trim($value) === '') {
            throw new \DomainException('A fleet id cannot be empty.');
        }
    }
}
