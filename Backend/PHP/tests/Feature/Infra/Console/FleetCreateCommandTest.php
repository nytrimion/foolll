<?php

declare(strict_types=1);

namespace Fulll\Tests\Feature\Infra\Console;

use Fulll\Domain\ValueObject\FleetId;
use Fulll\Infra\Console\FleetConsole;
use Fulll\Infra\InMemory\InMemoryFleetRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Uid\Uuid;

final class FleetCreateCommandTest extends TestCase
{
    private InMemoryFleetRepository $fleetRepository;
    private CommandTester $tester;

    protected function setUp(): void
    {
        $this->fleetRepository = new InMemoryFleetRepository();
        $this->tester = new CommandTester(
            FleetConsole::application($this->fleetRepository)->find('create'),
        );
    }

    public function testCreatesFleetAndPrintsItsId(): void
    {
        $this->tester->execute(['userId' => 'user-1']);

        $this->tester->assertCommandIsSuccessful();
        $fleetId = trim($this->tester->getDisplay());
        self::assertTrue(Uuid::isValid($fleetId));
        self::assertNotNull($this->fleetRepository->find(new FleetId($fleetId)));
    }

    public function testFailsWhenUserIdIsEmpty(): void
    {
        $exitCode = $this->tester->execute(['userId' => '']);

        self::assertSame(Command::FAILURE, $exitCode);
    }
}
