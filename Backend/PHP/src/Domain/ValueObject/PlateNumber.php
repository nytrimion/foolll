<?php

declare(strict_types=1);

namespace Fulll\Domain\ValueObject;

final readonly class PlateNumber
{
    public function __construct(public string $value)
    {
        if (trim($value) === '') {
            throw new \DomainException('A plate number cannot be empty.');
        }
    }
}
