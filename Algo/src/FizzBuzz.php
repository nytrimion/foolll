<?php

declare(strict_types=1);

namespace Fulll\Algo;

final readonly class FizzBuzz
{
    public function __construct(private RuleCollection $rules)
    {
    }

    public function evaluate(int $n): string
    {
        return $this->rules->matchFirst($n)->label ?? (string) $n;
    }
}
