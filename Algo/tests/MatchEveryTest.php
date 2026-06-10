<?php

declare(strict_types=1);

namespace Fulll\Algo\Tests;

use Fulll\Algo\DivisibleBy;
use Fulll\Algo\MatchEvery;
use Fulll\Algo\RuleCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MatchEveryTest extends TestCase
{
    private MatchEvery $sut;

    protected function setUp(): void
    {
        $this->sut = new MatchEvery(
            new RuleCollection(
                new DivisibleBy(3, 'Fizz'),
                new DivisibleBy(5, 'Buzz'),
            ),
            'FizzBuzz',
        );
    }

    /**
     * @return iterable<string, array{int, bool}>
     */
    public static function divisibilityProvider(): iterable
    {
        // Every inner rule matches -> the composite matches.
        yield 'every divisor' => [15, true];
        yield 'larger multiple' => [30, true];

        // A single inner rule failing is enough to reject: this pins "all",
        // not "any". Were it "any", 3 and 5 would wrongly match.
        yield 'first divisor only' => [3, false];
        yield 'second divisor only' => [5, false];
        yield 'no divisor' => [1, false];
    }

    #[Test]
    #[DataProvider('divisibilityProvider')]
    public function testMatchesOnlyWhenEveryRuleMatches(int $number, bool $expected): void
    {
        self::assertSame($expected, $this->sut->matches($number));
    }

    #[Test]
    public function testCarriesItsOwnExplicitLabel(): void
    {
        self::assertSame('FizzBuzz', $this->sut->label);
    }
}
