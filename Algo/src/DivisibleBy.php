<?php

declare(strict_types=1);

namespace Fulll\Algo;

final readonly class DivisibleBy implements Rule
{
    public function __construct(
        private int $divisor,
        public string $label,
    ) {
    }

    public function matches(int $n): bool
    {
        return $n % $this->divisor === 0;
    }
}
