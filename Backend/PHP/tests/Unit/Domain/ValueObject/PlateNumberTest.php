<?php

declare(strict_types=1);

namespace Fulll\Tests\Unit\Domain\ValueObject;

use Fulll\Domain\ValueObject\PlateNumber;
use PHPUnit\Framework\TestCase;

final class PlateNumberTest extends TestCase
{
    public function testRejectsEmptyValue(): void
    {
        $this->expectException(\DomainException::class);

        new PlateNumber('   ');
    }
}
