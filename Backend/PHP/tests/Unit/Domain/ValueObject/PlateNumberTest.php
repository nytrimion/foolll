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

    public function testIsEqualToAnotherPlateWithSameValue(): void
    {
        $plateNumber = new PlateNumber('AB-123-CD');

        self::assertTrue($plateNumber->equals(new PlateNumber('AB-123-CD')));
        self::assertFalse($plateNumber->equals(new PlateNumber('XY-999-ZZ')));
    }
}
