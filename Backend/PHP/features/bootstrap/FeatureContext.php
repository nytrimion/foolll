<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Fulll\App\Calculator;

final class FeatureContext implements Context
{
    /** @var array<string, int> */
    private array $values = [];

    #[When('I multiply :a by :b into :var')]
    public function iMultiply(int $a, int $b, string $var): void
    {
        $this->values[$var] = (new Calculator())->multiply($a, $b);
    }

    #[Then(':var should be equal to :value')]
    public function shouldBeEqualTo(string $var, int $value): void
    {
        if (($this->values[$var] ?? null) !== $value) {
            throw new \RuntimeException(sprintf(
                '%s is expected to be equal to %s, got %s',
                $var,
                $value,
                $this->values[$var] ?? 'null',
            ));
        }
    }
}
