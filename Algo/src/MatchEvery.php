<?php

declare(strict_types=1);

namespace Fulll\Algo;

final readonly class MatchEvery implements Rule
{
    public function __construct(
        private RuleCollection $rules,
        public string $label,
    ) {
    }

    public function matches(int $n): bool
    {
        return $this->rules->matchEvery($n);
    }
}
