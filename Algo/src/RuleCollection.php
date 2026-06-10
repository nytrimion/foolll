<?php

declare(strict_types=1);

namespace Fulll\Algo;

final readonly class RuleCollection
{
    /** @var array<array-key, Rule> */
    private array $rules;

    public function __construct(Rule ...$rules)
    {
        $this->rules = $rules;
    }

    public function matchFirst(int $n): ?Rule
    {
        return array_find(
            $this->rules,
            static fn (Rule $rule): bool => $rule->matches($n),
        );
    }
}
