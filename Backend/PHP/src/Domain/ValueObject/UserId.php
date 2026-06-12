<?php

declare(strict_types=1);

namespace Fulll\Domain\ValueObject;

final readonly class UserId
{
    public function __construct(public string $value)
    {
        if (trim($value) === '') {
            throw new \DomainException('A user id cannot be empty.');
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
