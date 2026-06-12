<?php

declare(strict_types=1);

namespace Fulll\Tests\Unit\Domain\ValueObject;

use Fulll\Domain\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

final class UserIdTest extends TestCase
{
    public function testRejectsEmptyValue(): void
    {
        $this->expectException(\DomainException::class);

        new UserId('   ');
    }

    public function testIsEqualToAnotherIdWithSameValue(): void
    {
        $userId = new UserId('user-1');

        self::assertTrue($userId->equals(new UserId('user-1')));
        self::assertFalse($userId->equals(new UserId('user-2')));
    }
}
