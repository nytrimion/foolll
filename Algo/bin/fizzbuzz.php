<?php

declare(strict_types=1);

use Fulll\Algo\DivisibleBy;
use Fulll\Algo\FizzBuzz;
use Fulll\Algo\MatchEvery;
use Fulll\Algo\RuleCollection;

require __DIR__ . '/../vendor/autoload.php';

$fizz = new DivisibleBy(3, 'Fizz');
$buzz = new DivisibleBy(5, 'Buzz');

// Order carries priority: matchFirst returns the first match, so the most
// specific rule (the FizzBuzz composite) must come first.
$fizzBuzz = new FizzBuzz(new RuleCollection(
    new MatchEvery(new RuleCollection($fizz, $buzz), 'FizzBuzz'),
    $fizz,
    $buzz,
));

foreach (range(1, 100) as $number) {
    echo $fizzBuzz->evaluate($number), PHP_EOL;
}
