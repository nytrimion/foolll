<?php

declare(strict_types=1);

namespace Fulll\Algo;

final class FizzBuzz
{
    public function evaluate(int $n): string
    {
        if ($n % 3 === 0 && $n % 5 === 0) {
            return 'FizzBuzz';
        }

        if ($n % 3 === 0) {
            return 'Fizz';
        }

        if ($n % 5 === 0) {
            return 'Buzz';
        }

        return (string) $n;
    }
}
