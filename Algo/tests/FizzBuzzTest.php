<?php

declare(strict_types=1);

namespace Fulll\Algo\Tests;

use Fulll\Algo\FizzBuzz;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FizzBuzzTest extends TestCase
{
    private FizzBuzz $sut;

    protected function setUp(): void
    {
        $this->sut = new FizzBuzz();
    }

    /**
     * @return iterable<string, array{
     *     number: int,
     *     expected: string,
     * }>
     */
    public static function numberProvider(): iterable
    {
        // Not divisible by 3 or 5 -> the number itself.
        yield 'one' => ['number' => 1, 'expected' => '1'];
        yield 'minus one' => ['number' => -1, 'expected' => '-1'];

        // Divisible by 3 only -> Fizz.
        yield 'three' => ['number' => 3, 'expected' => 'Fizz'];
        yield 'minus three' => ['number' => -3, 'expected' => 'Fizz'];

        // Divisible by 5 only -> Buzz.
        yield 'five' => ['number' => 5, 'expected' => 'Buzz'];
        yield 'minus five' => ['number' => -5, 'expected' => 'Buzz'];

        // Divisible by both 3 and 5 -> FizzBuzz.
        yield 'fifteen' => ['number' => 15, 'expected' => 'FizzBuzz'];
        yield 'minus fifteen' => ['number' => -15, 'expected' => 'FizzBuzz'];

        // Zero is divisible by everything. The engine is a total function:
        // range validation (1..N) is the caller's job, not the engine's.
        yield 'zero' => ['number' => 0, 'expected' => 'FizzBuzz'];
    }

    #[Test]
    #[DataProvider('numberProvider')]
    public function testEvaluateReturnsLabel(int $number, string $expected): void
    {
        self::assertSame($expected, $this->sut->evaluate($number));
    }

    #[Test]
    public function testEvaluatesContiguousSequence(): void
    {
        $actual = array_map($this->sut->evaluate(...), range(1, 15));

        self::assertSame(
            ['1', '2', 'Fizz', '4', 'Buzz', 'Fizz', '7', '8', 'Fizz', 'Buzz', '11', 'Fizz', '13', '14', 'FizzBuzz'],
            $actual,
        );
    }
}
