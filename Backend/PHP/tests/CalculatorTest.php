<?php

declare(strict_types=1);

namespace Fulll\Tests;

use Fulll\App\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function testItMultipliesTwoIntegers(): void
    {
        self::assertSame(15, (new Calculator())->multiply(3, 5));
    }
}
